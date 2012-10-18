<?php

/*
 * $author-maltesh@airpush.com
 */


interface ImageConstants{
    const IMAGE_PATH = '/var/www/api_cleancopy/test/images/';
}



class ImageResize implements ImageConstants {


    private $path;
    private $image_width;
    private $image_height;
    private $image_type;
    private $image_resource;
    private $new_image_resource;

    public $new_height;
    public $new_width;

    function __construct($path,$new_height,$new_width,$url=false){

        if(!empty($path)){
            // if the file name has space in it, encode it properly
            $this->path= $path;
        }else{
            throw new Exception('Image path not available');
        }

        $this->new_height = $new_height;
        $this->new_width  = $new_width;
        if($url==true){
            $this->downLoadImage();
            $this->createNewImage();
            $this->copyResizedImage();
            $this->copyFileLocally();
        }
    }
    ////function_exists( 'exif_imagetype' )


    private function downLoadImage(){

        $image_src            = imagecreatefromstring(file_get_contents($this->path));
        $this->image_resource = $image_src;
        $this->image_height   = imagesy($this->image_resource);
        $this->image_width    = imagesx($this->image_resource);
        $this->setImageType(exif_imagetype($this->path));
        //Image type will be 2 or 3 since we are supporting only png,jpeg,jpg
    }

    public function getImageWidth(){
        return $this->image_width;
    }

    public function getImageHeight(){
        return $this->image_height;
    }

    /*
     * Customize according to the needs
     */

    public function setImageType($image_type){

        $temp ='';
        switch ($image_type){
            case IMAGETYPE_JPEG:
                //2 for both jpg and jpeg
                $temp= 'jpeg';
                break;
            case IMAGETYPE_PNG:
                //3
                $temp ='png';
                break;
//            case IMAGETYPE_BMP:
//                //6
//                return 'bmp';
            default:
                break;
        }
        $this->image_type = $temp;
    }


    public function getImageType(){
       return  $this->image_type ;
    }

    private function createNewImage(){
        $this->new_image_resource = imagecreatetruecolor($this->new_width, $this->new_height);
    }


    private function copyResizedImage(){
        imagecopyresized($this->new_image_resource, $this->image_resource, 0, 0, 0, 0, $this->new_width, $this->new_height, $this->getImageWidth(), $this->getImageHeight());
    }


    private function copyFileLocally(){

        $image_convert_function ='image'.$this->getImageType();

        $random_string = $this->getRandomString();
        $path = self::IMAGE_PATH.$random_string.'.'.$this->getImageType();
        echo $image_convert_function. '=====\n';
        $image_convert_function($this->new_image_resource,$path);
    }


    public  function getRandomString(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++){
                $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }




    public function __destruct() {

        imagedestroy($this->image_resource);
        imagedestroy($this->new_image_resource);

    }
}



$image = new ImageResize('http://4.bp.blogspot.com/-54mMfVRIguw/UH3m5ZRPXOI/AAAAAAAAJ1g/K6uHNoGtwpA/s500/GOOGLE_CBF_009.jpg',72,72,true);

?>
