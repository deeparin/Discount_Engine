<?php

if (!function_exists('debug'))
{
    /**
     * บันทึกข้อความหรือค่าทุกชนิดไปยังไฟล์ Log ด้วยระดับ 'debug'
     *
     * @param mixed $data ข้อมูลอะไรก็ได้
     * @return void
     */
    function debug($data)
    {
        // แปลงข้อมูลทุกชนิดให้เป็นข้อความ
        $text = var_export($data, true);

        // บันทึกลง log
        log_message('debug', $text);
    }
}

?>
