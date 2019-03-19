<?php
namespace Sanket\Test\Api;

use Sanket\Test\Api\Data\ImageInterface;
interface ImageRepositoryInterface
{    
    public function save(ImageInterface $image);   
    public function getById($imageId);
    public function delete(ImageInterface $image);
    public function deleteById($imageId);
}
