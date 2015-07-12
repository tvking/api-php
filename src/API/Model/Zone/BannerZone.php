<?php

namespace GroupByInc\API\Model;

class BannerZone extends AbstractContentZone
{
    /**
     * @return string
     */
    public function getBannerUrl()
    {
        return $this->getContent();
    }

    /**
     * @param string $bannerUrl
     *
     * @return BannerZone
     */
    public function setBannerUrl($bannerUrl)
    {
        return $this->setContent($bannerUrl);
    }

    /**
     * @return string The type of zone.
     */
    public function getType()
    {
        return Zone\Type::Banner;
    }
}