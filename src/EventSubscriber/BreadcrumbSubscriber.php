<?php

namespace App\EventSubscriber;

use App\Entity\CallOfProject;
use App\Entity\Dictionary;
use App\Entity\Group;
use App\Entity\Invitation;
use App\Entity\OrganizingCenter;
use App\Entity\Project;
use App\Entity\ProjectFormLayout;
use App\Entity\Report;
use App\Entity\User;
use App\Utils\Breadcrumb\Breadcrumb;
use App\Utils\Breadcrumb\BreadcrumbItem;
use App\Utils\Breadcrumb\BreadcrumbManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;

class BreadcrumbSubscriber implements EventSubscriberInterface
{

    const TRANSLATION_PREFIX = 'app.breadcrumb.main.';

    /**
     * @var BreadcrumbManager
     */
    private $breadcrumb;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(BreadcrumbManager $breadcrumb, RouterInterface $router, EntityManagerInterface $em)
    {
        $this->breadcrumb = $breadcrumb;
        $this->router = $router;
        $this->em = $em;
    }


    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::CONTROLLER => [
                ['controller']
            ],
        ];
    }

    public function controller(ControllerEvent $event)
    {

        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $request = $event->getRequest();
        $route = $request->get('_route');

        $mainBreadCrumb = new Breadcrumb(BreadcrumbManager::BREADCRUMB_MAIN);

        //For every page  we add home to the breadcrumb
        $homeItem = new BreadcrumbItem(self::TRANSLATION_PREFIX . 'app.home.label', $this->router->generate('app.homepage'));
        $mainBreadCrumb->addItem($homeItem);

        $items = [
            'app.call_of_project' => [
                'path' =>'app.call_of_project.index',
                'entity' => [
                    'class' => CallOfProject::class,
                    'method' => 'find',
                    'parameter' => 'id',
                    'path' => 'app.call_of_project.informations',
                    'labelBy' => 'name'
                ],

            ],
            'app.project' => [
                'path' => 'app.project.index',
                'entity' => [
                    'class' => Project::class,
                    'method' => 'find',
                    'parameter' => 'id',
                    'path' => 'app.project.show',
                    'labelBy' => 'name'
                ],
            ],
            'app.report' => [
                'path' => 'app.report.index',
                'entity' => [
                    'class' => Report::class,
                    'method' => 'find',
                    'parameter' => 'id',
                    'path' => 'app.report.show',
                    'labelBy' => 'name'
                ],
            ],
            'app.admin.user' => [
                'path' => 'app.admin.user.index',
                'entity' => [
                    'class' => User::class,
                    'method' => 'find',
                    'parameter' => 'id',
                    'path' => 'app.admin.user.show',
                    'labelBy' => 'username'
                ],
            ],
            'app.admin.organizing_center' => [
                'path' => 'app.admin.organizing_center.index',
                'entity' => [
                    'class' => OrganizingCenter::class,
                    'method' => 'find',
                    'parameter' => 'id',
                    'path' => 'app.admin.organizing_center.show',
                    'labelBy' => 'name'
                ],
            ],
            'app.admin.project_form_layout' => [
                'path' => 'app.admin.project_form_layout.index',
                'entity' => [
                    'class' => ProjectFormLayout::class,
                    'method' => 'find',
                    'parameter' => 'id',
                    'path' => 'app.admin.project_form_layout.show',
                    'labelBy' => 'name'
                ],
            ],
            'app.admin.dictionary' => [
                'path' => 'app.admin.dictionary.index',
                'entity' => [
                    'class' => Dictionary::class,
                    'method' => 'find',
                    'parameter' => 'id',
                    'path' => 'app.admin.dictionary.edit',
                    'labelBy' => 'name'
                ],
            ],
            'app.admin.group' => [
                'path' => 'app.admin.group.index',
                'entity' => [
                    'class' => Group::class,
                    'method' => 'find',
                    'parameter' => 'id',
                    'path' => 'app.admin.group.show',
                    'labelBy' => 'name'
                ],
            ],
            'app.user' => [
                'path' => 'app.user.show_my_profile',
            ],
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($items as $itemRoute => $info) {

            //base route
            if (strpos($route, $itemRoute) !== false) {

                $breadcrumbItem = new BreadcrumbItem(
                    self::TRANSLATION_PREFIX . $itemRoute . '.label',
                    $this->router->generate($info['path'])
                );

                $mainBreadCrumb->addItem($breadcrumbItem);
            }

            //if entity concerned
            if (isset($info['entity'])) {
                $pathAttributes = array_intersect_key($request->attributes->all(), array_flip([$info['entity']['parameter']]));

                if (count($pathAttributes) > 0) {
                    $method = $info['entity']['method'];
                    $parameter = $pathAttributes[$info['entity']['parameter']];

                    $error = false;
                    $entity = null;
                    try {
                        $entity = $this->em->getRepository($info['entity']['class'])->$method($parameter);
                    } catch (\Exception $e)
                    {
                        $error = true;
                    }

                    if ($entity and !$error) {
                        $label = $propertyAccessor->getValue($entity, $info['entity']['labelBy']);

                        $breadcrumbItem = new BreadcrumbItem(
                            $label,
                            $this->router->generate($info['entity']['path'], $pathAttributes)
                        );

                        $mainBreadCrumb->addItem($breadcrumbItem);
                    }

                }

            }

        }

        //Deeper item
        $exceptionsRoutes = array_map(function ($item) {
            return $item['path'];
        }, $items);
        $exceptionsRoutes[] = 'app.homepage';
        $exceptionsRoutes[] = 'app.process_after_shibboleth_connection';
        if (strpos($route, 'app.') !== false and !in_array($route, $exceptionsRoutes)) {

            $breadcrumbItem = new BreadcrumbItem(
                self::TRANSLATION_PREFIX . $route,
                $this->router->generate($route, $pathAttributes ?? [])
            );

            $mainBreadCrumb->addItem($breadcrumbItem);
        }

        $this->breadcrumb->addBreadcrumb($mainBreadCrumb);

    }

}
