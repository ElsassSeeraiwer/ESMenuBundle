<?php

namespace ElsassSeeraiwer\ESMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use ElsassSeeraiwer\ESMenuBundle\Entity\MenuElement;
use ElsassSeeraiwer\ESMenuBundle\Form\NewMenuElementType;

/**
 * @Route("/menu")
 */
class MenuController extends Controller
{
    public $img_arrowUp = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAABSklEQVR42mNkoDFgHLVg1IJhZEFlVZXjt6/f8v7//8fCxc09raO9fTvVLMjKzg7i5uLqCw0Ll2dhYfm/Yd3aR6/fviuaPm3qOootyMrKduLj55tnZ2cvx8bGzvifgeH/718/GQ4dOvjo06dPSdOmTt1HtgUZmZlOPNzcfS5uHvq/f//5//v3b5D6/6ysrAysLMwMe/fsvPTl6zegT6btI9mC4JAQMx1tnTUWltayP37+YgCGPVA1UPl/kOx/IJOJgYODjeHEsaOPr1+/HrJq1cpTJFkgJSXplZyculFIWIT53/9/YJdD1f+HqWFiZGJ8+/bNn7lzZvs/f/58G0kWsLGxqQoLC7sAmRwEgvL727dv9/z69es2SRYEBgWxHj9+nJORARgWcHf/BwYVKHgYIVrB4v//WVnbfF+7ZvVvkiOZGmDUglELhoAFAAZiexnJTD3HAAAAAElFTkSuQmCC';

    public $img_arrowDown = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAABbklEQVR42mNkoDFgHLVg1IIhbEF+foH+379/uv7+/cv2//9/hv8gQRANxIyMQG1AzATEzMwsv9g5OMp6e7ovkmSBhISER2tb+3o3N3f2r1+/gdQBTQZCqF0gwM3Nzbhr144f1VWVgS9evNhBkgVAzVqurm6N/gGBPtIyshwfPrz/D3Y2yBtAICAgyPD0yeOfGzes37J79676r1+/XiPJAmsbG+4L58/rOju7VPoHBPjJySv8//LlK9gnPDzcDI8ePmDYuGHD5r1797QbGRtfPnzo0FeSLAABF1c3zsOHDkq5ubn129s7uGpq63CAQura1as/Dh08sAsIiuzt7Z8B6e8kRzIMAA3nPHr0qL6trU2ptY2tFyiAjh45vPXIkSM91tY2F3ft2vkdn36ikqmDoyPP6VOntPn4+AxB/E+fPp03NTW7euDA/i+E9BKdD6ysrdlu3bzFDgoiVVW1n8ePH/tFjL5hnJNHLRi1gGgAAPYXkRlob6juAAAAAElFTkSuQmCC';

    public $colorLvl = array('000000', 'C3C6CF', 'BDE6E8', 'DBFFA1', 'FFD182', 'FFA361');

    /**
     * @Route("/")
     * @Template()
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ElsassSeeraiwerESMenuBundle:MenuElement');

        $rootNodes = $repo->findBy(
            array('lvl' => 0),
            array('id'  => 'asc')
        );

        return $this->processList($rootNodes);
    }

    private function processList($rootNodes)
    {
        $em = $this->getDoctrine()->getManager();
        $titleRootNodes = $slugRootNodes = $htmlTrees = array();
        $repo = $em->getRepository('ElsassSeeraiwerESMenuBundle:MenuElement');

        $self = $this;

        foreach ($rootNodes as $rootNode) {
            $titleRootNodes[$rootNode->getId()] = $rootNode->getTitle();
            $slugRootNodes[$rootNode->getId()] = $rootNode->getSlug();

            $htmlTrees[$rootNode->getId()] = $repo->childrenHierarchy(
                $rootNode,
                false,
                array(
                    'decorate' => true,
                    'html' => true,
                    'rootOpen' => '',
                    'rootClose' => '',
                    'childOpen' => function($node ,$A) use ($self) {
                        $color = 'FF7894';
                        if($node['lvl'] <= 5)
                        {
                            $color = $self->colorLvl[$node['lvl']];
                        }
                        return '<tr style="background-color:#'.$color.';">';
                    },
                    'childClose' => function($node) {
                        return '</tr>';
                    },
                    'nodeDecorator' => function($node) use ($self) {
                        $html = '<td style="text-align:center;vertical-align: middle;">';
                            $html.= '<a href="'.$self->generateURL('elsassseeraiwer_esmenu_menu_indexbyid', array('id'=>$node['id'])).'">';
                            $html.= $node['id'].'</a>';
                        $html.= '</td>';
                        $html.= '<td style="text-align:center;vertical-align: middle;">';
                            $html.= $node['lvl'];
                        $html.= '</td>';
                        $html.= '<td>';
                            $html.= '<input data-id="'.$node['id'].'" data-origin="'.htmlspecialchars($node['title']).'" style="width:95%;" type="text" value="'.htmlspecialchars($node['title']).'" name="'.$node['id'].'_title" id="'.$node['id'].'_title" class="editTitleForm"/>';
                        $html.= '</td>';
                        $html.= '<td>';
                            $html.= '<input data-id="'.$node['id'].'" data-origin="'.$node['link'].'" style="width:95%;" type="text" value="'.$node['link'].'" name="'.$node['id'].'_link" id="'.$node['id'].'_link" class="editLinkForm"/>';
                        $html.= '</td>';
                        $html.= '<td>';
                            $html.= '<input data-id="'.$node['id'].'" data-origin="'.htmlspecialchars($node['params']).'" style="width:95%;" type="text" value="'.htmlspecialchars($node['params']).'" name="'.$node['id'].'_params" id="'.$node['id'].'_params" class="editParamsForm"/>';
                        $html.= '</td>';
                        $html.= '<td>';
                            $html.= '<a href="#" onclick="moveUp(\''.$node['id'].'\')"><img style="background-color:green;border:1px solid lightgrey;vertical-align:middle;width:24px;height:24px" title="moveUp" alt="moveUp" src="'.$self->img_arrowUp.'" /></a>&nbsp;';
                            $html.= '<a href="#" onclick="moveDown(\''.$node['id'].'\')"><img style="background-color:green;border:1px solid lightgrey;vertical-align:middle;width:24px;height:24px" title="moveDown" alt="moveDown" src="'.$self->img_arrowDown.'" /></a>&nbsp;';
                            $html.= '<input type="button" value="Add Children" onclick="addChildren(\''.$node['id'].'\')" style="background-color:green;padding:4px 8px;color:white;"/>&nbsp;';
                            $html.= '<input type="button" value="Add Brother" onclick="addBrother(\''.$node['id'].'\')" style="background-color:green;padding:4px 8px;color:white;"/>&nbsp;';
                            $html.= '<input type="button" value="Delete" onclick="removeMenuElem(\''.$node['id'].'\')" style="background-color:red;color:white;padding:4px 8px;font-weight:bold;"/>&nbsp;';
                        $html.= '</td>';
                        return $html;
                    }
                )
            );
        }

        return array(
            'htmlTrees'         => $htmlTrees,
            'titleRootNodes'    => $titleRootNodes,
            'slugRootNodes'     => $slugRootNodes,
        );
    }

    /**
     * @Route("/add/")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $menu = new MenuElement();
        $menuFirstChild = new MenuElement();

        $form = $this->createForm(new NewMenuElementType(), $menu, array(
            'action' => $this->generateUrl('elsassseeraiwer_esmenu_menu_add'),
            'method' => 'POST'
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $menuFirstChild->setTitle($menu->getTitle().' first element');
            $menuFirstChild->setParent($menu);

            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->persist($menuFirstChild);
            $em->flush();

            return $this->redirect($this->generateUrl('elsassseeraiwer_esmenu_menu_index'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/move/{id}/up/")
     * @ParamConverter("menuElem", class="ElsassSeeraiwerESMenuBundle:MenuElement")
     * @Template()
     * @Method("POST")
     */
    public function moveUpAction(Request $request, MenuElement $menuElem)
    {        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ElsassSeeraiwerESMenuBundle:MenuElement');

        $repo->moveUp($menuElem, 1);

        return new Response("OK");
    }   

    /**
     * @Route("/move/{id}/down/")
     * @ParamConverter("menuElem", class="ElsassSeeraiwerESMenuBundle:MenuElement")
     * @Template()
     * @Method("POST")
     */
    public function moveDownAction(Request $request, MenuElement $menuElem)
    {        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ElsassSeeraiwerESMenuBundle:MenuElement');

        $repo->moveDown($menuElem, 1);

        return new Response("OK");
    }   

    /**
     * @Route("/add/{id}/brother/")
     * @ParamConverter("brotherElem", class="ElsassSeeraiwerESMenuBundle:MenuElement")
     * @Template()
     * @Method("POST")
     */
    public function addBrotherAction(Request $request, MenuElement $brotherElem)
    {
        $title = $brotherElem->getTitle().' new brother';

        $menuElem = new MenuElement();
        $menuElem->setTitle($title);
        $menuElem->setParent($brotherElem->getParent());
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($menuElem);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/add/{id}/children/")
     * @ParamConverter("parentElem", class="ElsassSeeraiwerESMenuBundle:MenuElement")
     * @Template()
     * @Method("POST")
     */
    public function addChildrenAction(Request $request, MenuElement $parentElem)
    {
        $title = $parentElem->getTitle().' new children';

        $menuElem = new MenuElement();
        $menuElem->setTitle($title);
        $menuElem->setParent($parentElem);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($menuElem);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/remove/{id}/")
     * @ParamConverter("menuElem", class="ElsassSeeraiwerESMenuBundle:MenuElement")
     * @Template()
     * @Method("POST")
     */
    public function removeAction(Request $request, MenuElement $menuElem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($menuElem);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/modify/{id}/title/")
     * @ParamConverter("menuElem", class="ElsassSeeraiwerESMenuBundle:MenuElement")
     * @Template()
     * @Method("POST")
     */
    public function modifyTitleAction(Request $request, MenuElement $menuElem)
    {
        $title = $this->getRequest()->request->get('title');

        $menuElem->setTitle($title);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($menuElem);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/modify/{id}/link/")
     * @ParamConverter("menuElem", class="ElsassSeeraiwerESMenuBundle:MenuElement")
     * @Template()
     * @Method("POST")
     */
    public function modifyLinkAction(Request $request, MenuElement $menuElem)
    {
        $link = $this->getRequest()->request->get('link');

        $menuElem->setLink($link);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($menuElem);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/modify/{id}/params/")
     * @ParamConverter("menuElem", class="ElsassSeeraiwerESMenuBundle:MenuElement")
     * @Template()
     * @Method("POST")
     */
    public function modifyParamsAction(Request $request, MenuElement $menuElem)
    {
        $params = $this->getRequest()->request->get('params');

        $menuElem->setParams($params);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($menuElem);
        $em->flush();

        return new Response("OK");
    }


    /**
     * @Route("/view/{id}/")
     * @ParamConverter("menuElem", class="ElsassSeeraiwerESMenuBundle:MenuElement")
     * @Template()
     */
    public function viewAction(MenuElement $menuElem)
    {
        $title = $menuElem->getTitle();

        return array(
            'title' => $title
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
            'htmlTree'  => $htmlTree,
            'htmlTree2' => $htmlTree2,
        );
    }

    /**
     * @Route("/{id}/")
     * @Template("ElsassSeeraiwerESMenuBundle:Menu:index.html.twig")
     * @Method("GET")
     */
    public function indexByIdAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ElsassSeeraiwerESMenuBundle:MenuElement');

        $rootNodes = $repo->findBy(array('id' => $id));

        return $this->processList($rootNodes);
    }


}
