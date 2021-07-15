<?php namespace App\Services\ProductImage;


use App\Interfaces\ProductImageRepositoryInterface;
use App\Services\FileManagers\CdnFileManager;
use App\Services\Product\ProductFileManager;
use Illuminate\Http\UploadedFile;

class Creator
{
    use ProductFileManager, CdnFileManager;

    protected $productId;
    protected $images;
    protected $imagesLinks;
    protected ?UploadedFile $appThumb;
    /** @var ProductImageRepositoryInterface */
    protected ProductImageRepositoryInterface $productImageRepositoryInterface;
    protected string $filename;

    public function __construct(ProductImageRepositoryInterface $productImageRepositoryInterface)
    {
        $this->productImageRepositoryInterface = $productImageRepositoryInterface;
    }

    /**
     * @param mixed $productId
     * @return Creator
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @param mixed $images
     * @return Creator
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @param mixed $filename
     * @return Creator
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }


    /**
     * @param mixed $appThumb
     * @return Creator
     */
    public function setAppThumb($appThumb)
    {
        $this->appThumb = $appThumb;
        return $this;
    }

    public function saveImagesLinks($imagesLinks)
    {
        $data = [];
        $productId = $this->productId;
        collect($imagesLinks)->each(function($image) use (&$data, $productId){
            array_push($data, [
                'product_id' => $productId,
                'image_link' => $image
            ]);
        });
        return $this->productImageRepositoryInterface->insert($data);
    }

    private function saveImages($image_gallery)
    {
        $image_gallery_link = [];
        foreach ($image_gallery as $key => $file) {
            if (!empty($file)) {
                list($file, $filename) = $this->makeProductImages($file, '_' . getFileName($file) . '_product_image');
                $image_gallery_link[] = $this->saveFileToCDN($file, getPosServiceImageGalleryFolder(), $filename);
            }
        }
        return json_encode($image_gallery_link);
    }

    public function create()
    {
        $this->imagesLinks = $this->saveImages($this->images);
        return $this->saveImagesLinks(json_decode($this->imagesLinks, true));
    }

    public function createAppThumb(): string
    {
        /** @var UploadedFile $avatar */
        /** @var string $avatar_filename */
        list($avatar, $avatar_filename) = $this->makeProductAppThumb($this->appThumb, $this->filename);
        return $this->saveImageToCDN($avatar, getPosServiceThumbFolder(), $avatar_filename);
    }
}
