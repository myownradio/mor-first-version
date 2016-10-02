#!/usr/bin/perl

# myownradio.biz audio lores making script

use FindBin qw($Bin);
use threads;
use threads::shared;
use DBI;
use JSON;
use Encode qw/encode decode/;

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

my $root = "/usr/domains/domain_content/myownradio.biz/";
my $ffmpeg = "/usr/local/bin/ffmpeg";

my $number_of_threads = 2;
my @threads = ();

for $y (1...$number_of_threads) {
	push @threads, threads->create( \&processingThread ); 
}

push @threads, threads->create( \&gettingThread ); 

foreach $thread(@threads) { $thread->join(); }



sub gettingThread {
	while(1) {
		$dbh = DBI->connect("dbi:mysql:$db_database:$db_hostname", $db_login, $db_password);
        $dbh->do("SET NAMES 'utf8'");
		$qr = $dbh->prepare("SELECT * FROM `r_tracks` WHERE `lores` = 0");
		$qr->execute();
		while($row = $qr->fetchrow_hashref) {
            foreach $i (@job_running) {
                next if($i == $row->{tid});
            }
			push(@job_queue, encode_json($row));
		}
		$dbh->disconnect();
		sleep(5);
		waitThreads();
	}
}

sub updateDatabase {
    my $tid = shift;
	$dbh = DBI->connect("dbi:mysql:$db_database:$db_hostname", $db_login, $db_password);
	$qr = $dbh->prepare("UPDATE `r_tracks` SET `lores` = 1 WHERE `tid` = ?");
	$qr->execute($tid);
	$dbh->disconnect();
}

sub processingThread {

	my $tid = threads->tid();

	while(1) {
        unless($job = shift @job_queue) {
            sleep 1;
            next;
        }
        
     
        $job_decoded = decode_json($job);

        $job_running[$tid] = $job_decoded->{tid};
        
        # Making lores audio file
        my $src_file = esc_chars($root . sprintf("content/ui_%d/a_%03d_%s", $job_decoded->{uid}, $job_decoded->{tid}, decode('UTF-8', $job_decoded->{filename})));
        my $dst_file = $root . sprintf("content/ui_%d/lores_%03d.mp3", $job_decoded->{uid}, $job_decoded->{tid});
        my $run_cmd = "/usr/local/bin/ffmpeg -i $src_file -map_metadata -1 -vn -ar 44100 -ac 2 -ab 160k -acodec libmp3lame -f mp3 - 2>>/dev/null";
        
        open(PROC, "$run_cmd|");
        binmode PROC;

        open(DST, ">", $dst_file);
        my $hC = 1;
		while($hC == 1) {
			read(PROC, $header, 3);
			if($header eq 'ID3') {
				read($proc, $ver, 3);
				read($proc, $sizec, 4);
				$size = unpack("N", $sizec);
				$size = $size[1];
				read($proc, $id3, $size);
			} else {
				$hC = 0;
                print DST $header;
			}
		}
        
        my $buffer = "";
       
        while( !eof(PROC) ) {
            read(PROC, $buffer, 4096);
            print DST $buffer;
        }
        close(DST);
        
        $code = close(PROC);
        updateDatabase($job_decoded->{tid}) if($code == 1);
                
        $job_running[$tid] = 0;

	}
    
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
