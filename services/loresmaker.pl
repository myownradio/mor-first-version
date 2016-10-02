#!/usr/bin/perl

# myownradio.biz audio lores making script

use FindBin qw($Bin $RealBin);
use threads;
use threads::shared;
use DBI;
use JSON;
use Encode qw/encode decode/;
use Time::HiRes qw/time/;

use Data::Dumper;

exit if check_lock();

fork and exit;

create_lock();

$SIG{'INT'} = sub {
    terminate();
};

 
my @job_queue:shared = ();
my @job_status:shared = ();
my %job_running:shared = ();

my $db_hostname = "localhost";
my $db_database = "myownradio";
my $db_login = "root";
my $db_password = "";

my $log_file = $RealBin."/loresmaker.log";

my $root = "/media/www/myownradio.biz/";
my $ffmpeg = "/usr/local/bin/ffmpeg";

# Processing Units
my %convertorHosts = (
	0 => [ '172.16.112.22',  'ssh -o ConnectTimeout=5 Roman@172.16.112.22' , '/usr/local/bin/ffmpeg', 4 ],
	1 => [ 'localhost',      'ssh -o ConnectTimeout=5 localhost'           , '/usr/local/bin/ffmpeg', 2 ],
#	2 => [ '91.231.229.130', 'ssh -o ConnectTimeout=5 LRU@91.231.229.130'  , '/usr/local/bin/ffmpeg', 2 ],
);

my @threads = ();

foreach my $key (sort keys %convertorHosts) {
	my $connectHost   = $convertorHosts{$key}[0];
	my $connectString = $convertorHosts{$key}[1];
	my $commandString = $convertorHosts{$key}[2];
	my $threadsCount  = $convertorHosts{$key}[3];
	for $i (1..$threadsCount)
	{
		print "Thread: #$i, Host: ", $connectHost, "\n";
		push @threads, threads->create( \&processingThread, $connectHost, $connectString, $commandString ); 
	}
}

push @threads, threads->create( \&gettingThread ); 

$_->join() foreach @threads;


sub gettingThread {
	while(1) {
		$dbh = DBI->connect("dbi:mysql:$db_database:$db_hostname", $db_login, $db_password);
	    $dbh->do("SET NAMES 'utf8'");
		$qr = $dbh->prepare("SELECT * FROM `r_tracks` WHERE `lores` = 0 ORDER BY `tid` ASC");
		$qr->execute();
		DB: while($row = $qr->fetchrow_hashref) {
            foreach $i (keys %job_running) {
                next DB if($job_running{$i} eq $row->{tid});
            }
			push(@job_queue, encode_json($row));
		}
		$dbh->disconnect();
		sleep(1);
		waitThreads();
	}
}

sub updateDatabase {
    my $tid = shift;
	my $length = shift;
	
	$dbh = DBI->connect("dbi:mysql:$db_database:$db_hostname", $db_login, $db_password);
	$dbh->do("SET NAMES 'utf8'");
	$qr = $dbh->prepare("UPDATE `r_tracks` SET `lores` = 1, `duration` = ? WHERE `tid` = ?");
	$qr->execute($length, $tid);
	$dbh->disconnect();
}

sub updateDatabaseError {
    my $tid = shift;
	
	$dbh = DBI->connect("dbi:mysql:$db_database:$db_hostname", $db_login, $db_password);
	$dbh->do("SET NAMES 'utf8'");
	$qr = $dbh->prepare("UPDATE `r_tracks` SET `lores` = -1 WHERE `tid` = ?");
	$qr->execute($tid);
	$dbh->disconnect();
}

sub processingThread {

	my $processHost = shift;
	my $connectString = shift;
	my $processString = shift;
	my $tid = threads->tid();

	PROC: while(1) {
        unless($job = shift @job_queue) {
            sleep 1;
            next;
        }
     
		printf "Thread %d(%s) starting to process file...\n", $tid, $processHost;
	 
        $job_decoded = decode_json($job);

        $job_running{$tid} = $job_decoded->{tid};
		
       
        # Making lores audio file
		my $source = $root . sprintf("content/ui_%d/a_%03d_original.%s", $job_decoded->{uid}, $job_decoded->{tid}, $job_decoded->{ext});

		if( -e $source ) {
			my $src_file = esc_chars($source);
			my $dst_file = $root . sprintf("content/ui_%d/lores_%03d.mp3", $job_decoded->{uid}, $job_decoded->{tid});
			my $run_cmd = sprintf("cat %s | %s %s -i - -map_metadata -1 -vn -ar 44100 -ac 2 -acodec libmp3lame -ab 256k -f mp3 - 2>/dev/null", $src_file, $connectString, $processString);
			my $st_time = time();
			open(PROC, "$run_cmd|");
			binmode PROC;
			open(DST, ">", $dst_file);

			my $buffer = "";
			while( !eof(PROC) ) {
				read(PROC, $buffer, 4096);
				print DST $buffer;
			}
			close(DST);
			close(PROC);
			
			$code = int(`echo $?`);
			
			$enc_dur = time - $st_time;
			
			my $new_length;
			
			$new_length = mediaLength($dst_file) if($code == 0) ;
			
			open(LOG,">>", $log_file);
			print LOG sprintf("%s [processor=%s,exit=%s,duration=%0.2fs,speed=%0.2fx]\n", $job_decoded->{tid} . ". " . $job_decoded->{artist} . " - " . $job_decoded->{title}, $processHost, $code, $enc_dur, $new_length / 1000 / $enc_dur);
			#print LOG $source, " => ", $dst_file, " [$code] done in " . $enc_dur . "s.\n";
			close(LOG);

			# Exit codes:
			# 0x0 - OK
			# 0xFF00 - No connection
			# 0x4500 - File corrupted
			
			
			if($code == 0) 
			{
				printf "Thread %d(%s) processing completed successfull\n", $tid, $processHost;
				
				updateDatabase($job_decoded->{tid}, $new_length);
				unlink decode('utf-8', $source);
			}
			elsif($code == 0x4500) 
			{
				printf "Thread %d(%s) processing error: file corrupted\n", $tid, $processHost;
				updateDatabaseError($job_decoded->{tid});
			}
			else
			{
				printf "Thread %d(%s) processing completed with errors\n", $tid, $processHost;
				push @job_queue, $job;
				$job_running{$tid} = 0;
				sleep 60;
				next PROC;
			}
		}    
		$job_running{$tid} = 0;
	}
}

sub mediaLength
{
	$file = shift;
	$command = sprintf("/usr/local/bin/mediainfo --Inform=\"General;%%Duration%%\" %s", esc_chars($file));
	$result = `$command`;
	chomp $result;
	return int($result);
}

sub waitThreads {
	while(left() > 0) {
		sleep 1;
	}
}

sub left {
	return ($#job_queue+1);
}

sub esc_chars {
    my $file = shift;
    $file =~ s/([\x22])/\\$1/g;
    return "\"$file\"";
}



sub terminate() 
{
    remove_lock();
    exit;
}

sub create_lock() 
{
    print "[LOCK] CREATING\n";
    open PD, ">", "$Bin/loresmaker.pid";
    print PD $$;
    close PD;
}

sub remove_lock() {
    print "[LOCK] REMOVING\n";
    unlink "$Bin/loresmaker.pid";
}

sub check_lock() {

    return 0 if (! -e "$Bin/loresmaker.pid");

    open PD, "<", "$Bin/loresmaker.pid";
    $pid = <PD>;
    close PD;
    chomp $pid;

    return 0 unless ( kill 0, $pid );

    print "[LOCK] LOCKED!\n";
    return 1;

}
