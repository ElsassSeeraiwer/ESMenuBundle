<?php

namespace ElsassSeeraiwer\ESMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use ElsassSeeraiwer\ESMenuBundle\Entity\MenuElement;
use ElsassSeeraiwer\ESMenuBundle\Form\NewMenuElementType;

/**
 * @Route("/menu")
 */
class MenuController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$arrayTrees = $htmlTrees = array();

    	$repo = $em->getRepository('ElsassSeeraiwerESMenuBundle:MenuElement');

    	$rootNodes = $repo->findByLvl(0);

    	foreach ($rootNodes as $rootNode) {
    		$arrayTrees[$rootNode->getTitle()] = $repo->childrenHierarchy($rootNode, false, array(), true);

	    	$htmlTrees[$rootNode->getTitle()] = $repo->childrenHierarchy(
	    		$rootNode,
	    		false,
	    		array(
			        'decorate' => true,
			        'html' => true,
		            'rootOpen' => '',
		            'rootClose' => '',
		            'childOpen' => function($node) {
				    	return '<tr>';
				    },
		            'childClose' => function($node) {
				    	return '</tr>';
				    },
				    'nodeDecorator' => function($node) {
				    	$html = '<td style="text-align:center;">'.$node['id'].'</td>';
				    	$html.= '<td style="text-align:center;">'.$node['lvl'].'</td>';
				    	$html.= '<td>'.$node['title'].'</td>';

				    	return $html;
				    }
				)
			);
    	}

        return array(
        	'arrayTrees' => $arrayTrees,
        	'htmlTrees'	=> $htmlTrees,
    	);
    }

    /**
     * @Route("/add/")
     * @Template()
     */
    public function addAction(Request $request)
    {
    	$menu = new MenuElement();

        $form = $this->createForm(new NewMenuElementType(), $menu, array(
            'action' => $this->generateUrl('elsassseeraiwer_esmenu_menu_add'),
            'method' => 'POST'
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();

            return $this->redirect($this->generateUrl('elsassseeraiwer_esmenu_menu_index'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/test/")
     * @Template()
     */
    public function testAction()
    {
    	$em = $this->getDoctrine()->getManager();

    	$repo = $em->getRepository('ElsassSeeraiwerESMenuBundle:MenuElement');

    	$rootNode = $repo->findOneByRoot(1);

    	$arrayTree = $repo->childrenHierarchy($rootNode);

    	$htmlTree = $repo->childrenHierarchy(
    		$rootNode,
    		false,
    		array(
		        'decorate' => true,
		        'html' => true
			)
		);

    	$htmlTree2 = $repo->childrenHierarchy(
    		$rootNode,
    		false,
    		array(
		        'decorate' => true,
		        'html' => true,
			    'nodeDecorator' => function($node) {
			        return '<a href="http://www.google.fr"><span>'.$node['title'].'</span></a>';
			    }
			)
		);

    	$htmlTree2 = $repo->childrenHierarchy(
    		$rootNode,
    		false,
    		array(
		        'decorate' => true,
		        'representationField' => 'title',
		        'html' => true,
	            'rootOpen' => function($tree) {
			    	if(count($tree) && $tree[0]['lvl'] <= 1)
			    	{
			        	return '<ul>';
			        }
			    },
	            'rootClose' => function($child) {
			    	if(count($child) && $child[0]['lvl'] <= 1)
			    	{
			        	return '</ul>';
			        }
			    },
	            'childOpen' => function($node) {
			    	if(count($node) && $node['lvl'] <= 1)
			    	{
			        	return '<li>';
			        }
			    },
	            'childClose' => function($node) {
			    	if(count($node) && $node['lvl'] <= 1)
			    	{
			        	return '</li>';
			        }
			    },
			    'nodeDecorator' => function($node) {
			    	if($node['lvl'] <= 1)
			    	{
			        	return '<a href="http://www.google.fr"><span>'.$node['title'].'</span></a>';
			        }
			    }
			)
		);

        return array(
        	'arrayTree' => $arrayTree,
        	'htmlTree'	=> $htmlTree,
        	'htmlTree2'	=> $htmlTree2,
    	);
    }


}
