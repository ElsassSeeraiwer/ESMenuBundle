<?php

namespace ElsassSeeraiwer\ESMenuBundle\Twig;

use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use \Twig_Filter_Method;
use \Twig_Function_Method;

class MenuExtension extends \Twig_Extension
{
    private $request;
    private $container;
	private $em;
    private $repo;
    private $env;
    private $options = array(
        'transEdit'             => false,
        'translation_domain'    => 'esMenu',
        'lvl1'                  => array(
            'ulClassname'           => 'esMenuUL',
            'liClassname'           => 'esMenuLI',
        ),
    );

    public function onKernelRequest(GetResponseEvent $event) {
        if ($event->getRequestType() === HttpKernel::MASTER_REQUEST) {
            $this->request = $event->getRequest();
        }
    }

    public function getFunctions()
    {
        return array(
            'esMenuByTitle' => new Twig_Function_Method($this, 'getESMenuTitle', array('needs_environment' => true, 'is_safe' => array('all'))),
            'esMenuById' => new Twig_Function_Method($this, 'getESMenuId', array('needs_environment' => true, 'is_safe' => array('all'))),
        );
    }

    public function getESMenuId($env, $id, $options = array())
    {
        $rootNode = $this->repo->findOneById($id);
        
        return $this->process($env, $rootNode, $options);
    }

    public function getESMenuTitle($env, $title, $options = array())
    {
        $rootNode = $this->repo->findOneByTitle($title);
        
        return $this->process($env, $rootNode, $options);
    }

    private function process($env, $rootNode, $options = array())
    {
        $this->env = $env;

        $this->options = array_merge($this->options, $options);

        $content = $this->repo->childrenHierarchy(
            $rootNode,
            false,
            array(
                'decorate' => true,
                'html' => true,
                'rootOpen' => function($tree) {
                    $currentLVL = $tree[0]['lvl'];
                    $classname = 
                        (isset($this->options['lvl'.$currentLVL]['ulClassname'])) ? 
                            $this->options['lvl'.$currentLVL]['ulClassname'] : 
                            'lvl'.$currentLVL ;
                    $id = 
                        (isset($this->options['lvl'.$currentLVL]['ulId'])) ? $this->options['lvl'.$currentLVL]['ulId'] : '' ;
                    return '<ul id="'.$id.'" class="'.$classname.'">';
                },
                'rootClose' => function($child) {
                    return '</ul>';
                },
                'childOpen' => function($node) {
                    $currentLVL = $node['lvl'];
                    $classname = 
                        (isset($this->options['lvl'.$currentLVL]['liClassname'])) ? 
                            $this->options['lvl'.$currentLVL]['liClassname'] : 
                            'lvl'.$currentLVL ;
                    $id = 'esMenuElem_'.$node['id'];
                    return '<li id="'.$id.'" class="'.$classname.'">';
                },
                'childClose' => function($node) {
                    return '</li>';
                },
                'nodeDecorator' => function($node) {
                    $link = $node['link'];
                    $params = json_decode(preg_replace("/([a-zA-Z0-9_]+?):/" , "\"$1\":", $node['params']), true);

                    if(substr($link,0,1) == '#')
                    {
                        return $this->env->render('ElsassSeeraiwerESMenuBundle:Menu:node.html.twig', array(
                            'title'     => $node['title'], 
                            'link'      => $link,
                            'options'   => $this->options,
                        ));
                    }
                    elseif(substr($link,0,7) == 'http://')
                    {
                        return $this->env->render('ElsassSeeraiwerESMenuBundle:Menu:node.html.twig', array(
                            'title'     => $node['title'], 
                            'link'      => $link,
                            'options'   => $this->options,
                        ));
                    }
                    elseif($link != '')
                    {
                        return $this->env->render('ElsassSeeraiwerESMenuBundle:Menu:node.html.twig', array(
                            'title'     => $node['title'], 
                            'pathname'  => $link, 
                            'params'    => $params,
                            'locale'    => $this->request->getLocale(),
                            'options'   => $this->options,
                        ));
                    }
                    else
                    {
                        return $this->env->render('ElsassSeeraiwerESMenuBundle:Menu:node.html.twig', array(
                            'title'     => $node['title'],
                            'options'   => $this->options,
                        ));
                    }
                }
            )
        );

        return $content;
    }

    public function getName()
    {
        return 'menu_twig_extension';
    }
    
    public function __construct($container, $entityManager)
    {
        $this->container = $container;
        $this->em = $entityManager;
        $this->repo = $this->em->getRepository('ElsassSeeraiwerESMenuBundle:MenuElement');
    }
}