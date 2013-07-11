<?php

namespace ElsassSeeraiwer\ESMenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuConfig
 *
 * @ORM\Table(name="es_menu_config")
 * @ORM\Entity(repositoryClass="ElsassSeeraiwer\ESMenuBundle\Entity\MenuConfigRepository")
 */
class MenuConfig
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="config_id", type="string", length=255)
     */
    private $configId;

    /**
     * @var text
     *
     * @ORM\Column(name="options", type="text", nullable=true)
     */
    private $options;

    /**
     * @ORM\ManyToOne(targetEntity="MenuElement")
     */
    private $menu;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set configId
     *
     * @param string $configId
     * @return MenuConfig
     */
    public function setConfigId($configId)
    {
        $this->configId = $configId;
    
        return $this;
    }

    /**
     * Get configId
     *
     * @return string 
     */
    public function getConfigId()
    {
        return $this->configId;
    }

    /**
     * Set menu
     *
     * @param \ElsassSeeraiwer\ESMenuBundle\Entity\MenuElement $menu
     * @return MenuConfig
     */
    public function setMenu(\ElsassSeeraiwer\ESMenuBundle\Entity\MenuElement $menu = null)
    {
        $this->menu = $menu;
    
        return $this;
    }

    /**
     * Get menu
     *
     * @return \ElsassSeeraiwer\ESMenuBundle\Entity\MenuElement 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set options
     *
     * @param string $options
     * @return MenuConfig
     */
    public function setOptions($options)
    {
        $this->options = $options;
    
        return $this;
    }

    /**
     * Get options
     *
     * @return string 
     */
    public function getOptions()
    {
        return $this->options;
    }
}