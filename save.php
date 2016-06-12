<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$imgData = $_POST['imgData'];
$imgName = $_POST['imgName'];
$s = base64_decode(str_replace('data:image/png;base64,', '', $imgData));
$source = 'file/' . $imgName . '.png';
// 存储图片：默认dpi为72
//file_put_contents($imgUrl, $s);
// 修改图片dpi为300并存储
read_png_dpi($source, $s);
// function修改图片dpi
function read_png_dpi($source, $file)
{
    //数据块长度为9
    $len = pack("N", 9);
    //数据块类型标志为pHYs
    $sign = pack("A*", "pHYs");
    //X方向和Y方向的分辨率均为300DPI（1像素/英寸=39.37像素/米），单位为米（0为未知，1为米）
    $data = pack("NNC", 300 * 39.37, 300 * 39.37, 0x01);
    //CRC检验码由数据块符号和数据域计算得到
    $checksum = pack("N", crc32($sign . $data));
    $phys = $len . $sign . $data . $checksum;

    $pos = strpos($file, "pHYs");
    if ($pos > 0) {
        //修改pHYs数据块
        $file = substr_replace($file, $phys, $pos - 4, 21);
    } else {
        //IHDR结束位置（PNG头固定长度为8，IHDR固定长度为25）
        $pos = 33;
        //将pHYs数据块插入到IHDR之后
        $file = substr_replace($file, $phys, $pos, 0);
    }
    // 存储图片
    file_put_contents($source, $file);
}
?>