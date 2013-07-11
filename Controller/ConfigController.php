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
use ElsassSeeraiwer\ESMenuBundle\Entity\MenuConfig;
use ElsassSeeraiwer\ESMenuBundle\Form\NewMenuConfigType;
use ElsassSeeraiwer\ESMenuBundle\Form\ModifyMenuConfigType;
/**
 * @Route("/menu/config")
 */
class ConfigController extends Controller
{
    private $defaultOptions = array(
        'transEdit'             =>  false,
        'translation_domain'    =>  'esMenu',
        'lvlmax'                =>  1
        'lvl1'                  =>  array(
            'ulClassname'           =>  'esMenuUL',
            'liClassname'           =>  'esMenuLI'
        ),
    );

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ElsassSeeraiwerESMenuBundle:MenuConfig');

        $configs = $repo->findBy(array(), array('configId'  => 'asc'));

        return array(
            'configs' => $configs,
        );
    }

    /**
     * @Route("/add/")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $config = new MenuConfig();
        $config->setOptions(json_encode($this->defaultOptions, JSON_PRETTY_PRINT));

        $form = $this->createForm(new NewMenuConfigType(), $config, array(
            'action' => $this->generateUrl('elsassseeraiwer_esmenu_config_add'),
            'method' => 'POST'
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($config);
            $em->flush();

            return $this->redirect($this->generateUrl('elsassseeraiwer_esmenu_config_index'));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/modify/{id}/")
     * @ParamConverter("config", class="ElsassSeeraiwerESMenuBundle:MenuConfig")
     * @Template()
     */
    public function modifyAction(Request $request, MenuConfig $config)
    {
        $form = $this->createForm(new ModifyMenuConfigType(), $config, array(
            'action' => $this->generateUrl('elsassseeraiwer_esmenu_config_modify', array('id' => $config->getId())),
            'method' => 'POST'
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($config);
            $em->flush();

            return $this->redirect($this->generateUrl('elsassseeraiwer_esmenu_config_index'));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/remove/{id}/")
     * @ParamConverter("config", class="ElsassSeeraiwerESMenuBundle:MenuConfig")
     * @Template()
     * @Method("POST")
     */
    public function removeAction(Request $request, MenuConfig $config)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($config);
        $em->flush();

        return new Response("OK");
    }

}
