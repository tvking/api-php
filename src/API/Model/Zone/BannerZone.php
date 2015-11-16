<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class BannerZone extends Zone
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $bannerUrl;

    /**
     * @return string The value set by the merchandiser.
     */
    public function getBannerUrl()
    {
        return $this->bannerUrl;
    }

    /**
     * @param string $bannerUrl Set the banner url.
     *
     * @return BannerZone
     */
    public function setBannerUrl($bannerUrl)
    {
        $this->bannerUrl = $bannerUrl;
        return $this;
    }

    /**
     * @return string The type of zone.
     */
    public function getType()
    {
        return Zone\Type::Banner;
    }
}