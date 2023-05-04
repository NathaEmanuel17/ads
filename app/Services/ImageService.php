<?php

namespace App\Services;


class ImageService
{
    public static function storeImages(
        array|object $images,
        string $pathToStore,
        string|int $propertyKey = 'propertyKey',
        string|int $propertyValue = null
    ) : array
    {

        // É apenas uma imagem (objeto de um imagem 'upada')?
        if(is_object($images)) {

            self::worksWithImage($images, $pathToStore);
        }

        // Temos um array de imagens

        $uploadedImages = [];

        foreach($images['images'] as $image){

            $uploadedImages[] = [
                $propertyKey => $propertyValue,
                'image'      => self::worksWithImage($image, $pathToStore)
            ];
        }

        return $uploadedImages;
    }

    private static function worksWithImage(object $image, string $pathToStore) : string
    {
        // Nesse ponto armazenamos a imagem no caminho informado.
        $imagePath = $image->store($pathToStore);

        // Fullpath de onde foi armazenado o arquivo
        $imagePath = WRITEPATH . "uploads/$imagePath";

        $imageSmallPath = WRITEPATH . "uploads/$pathToStore/small/";

        // Existe o diretório contido na variavel $imageSmallPath
        if(!is_dir($imageSmallPath)) {
            //não existe. Então podemos criá-lo
            mkdir($imageSmallPath);
        }

        // Manipulamos a imagem para criarmos uma copia um pouco menor que a original
        service('image')
            ->withFile($imagePath)  //arquivo original 
            ->resize(275, 275, true, 'center')
            ->save($imageSmallPath.$image->getName());

        return $image->getName();
    }
}

?>