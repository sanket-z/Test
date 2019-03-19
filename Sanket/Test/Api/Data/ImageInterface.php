<?php
namespace Sanket\Test\Api\Data;
interface ImageInterface
{
    const IMAGE_ID          = 'test_id';
    const IMAGE             = 'image';
    public function getId();
    public function getImage();
    public function setId($id);
    public function setImage($image);
}
