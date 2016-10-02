<?php

class misc
{

    static function get_audio_tags($filename)
    {

        setlocale(LC_ALL, "en_US.UTF-8");
        $fn_quote = escapeshellarg($filename);
        $fetch_cmd = config::getSetting('getters', 'mediainfo') . "  --Inform=\"General;%Duration%\\n%Title%\\n%Album%\\n%Performer%\\n%Genre%\\n%Album/Performer%\\n%Recordede_Date%\" " . $fn_quote;
        exec($fetch_cmd, $tag_data, $exit);

        $tag_list = array('DURATION', 'TITLE', 'ALBUM', 'PERFORMER', 'GENRE', 'ALBUM_PERFORMER', 'RECORDED_DATE');

        if (count($tag_data) != count($tag_list))
            return null;

        $tag_array = array_combine($tag_list, $tag_data);

        return $tag_array;
    }

    static function make_lores_file($src, $dst)
    {

        $buffer = 4096;
        $exec = config::getSetting('streaming', 'lores_create_cmd');
        $exec = str_replace('<INFILE>', escapeshellarg($src), $exec);

        $proc = popen($exec, "r");
        $out = fopen($dst, "w");

        $hC = true;
        while ($hC)
        {
            $header = fread($proc, 3); // strip metadata
            if ($header == 'ID3')
            {
                $ver = fread($proc, 3);
                $sizec = fread($proc, 4);
                $size = unpack("N", $sizec);
                $size = $size[1];
                $id3 = fread($proc, $size);
            } else
            {
                $hC = false;
                fwrite($out, $header);
            }
        }

        while ($data = fread($proc, $buffer))
        {

            fwrite($out, $data);
            flush();
        }

        fclose($out);

        return pclose($proc);
    }

    static function generateId()
    {
        $id_length = 6;
        $id_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $id = "";
        for ($i = 0; $i < $id_length; $i++)
            $id .= substr($id_chars, rand(0, strlen($id_chars) - 1), 1);
        return $id;
    }

    static function convertSecondsToTime($seconds)
    {
        return sprintf("%02d:%02d", $seconds / 60, $seconds % 60);
    }

}
