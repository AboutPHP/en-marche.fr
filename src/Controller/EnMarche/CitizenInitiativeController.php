<?php

namespace AppBundle\Controller\EnMarche;

use AppBundle\Entity\CitizenInitiative;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CitizenInitiativeController extends Controller
{
    use EntityControllerTrait;

    /**
     * @Route("/initiative citoyenne/creer", name="app_create_citizen_initiative")
     * @Method("GET|POST")
     */
    public function createEventAction(Request $request, CitizenInitiative $initiative): Response
    {
        $form = $this->createForm(EventCommandType::class, $initiative);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $this->get('app.event.handler')->handle($command);

            $registrationCommand = new EventRegistrationCommand($event, $this->getUser());
            $this->get('app.event.registration_handler')->handle($registrationCommand);

            $this->addFlash('info', $this->get('translator')->trans('committee.event.creation.success'));

            return $this->redirectToRoute('app_event_show', [
                'slug' => (string) $command->getEvent()->getSlug(),
            ]);
        }

        return $this->render('committee_manager/add_event.html.twig', [
            'initiative' => $initiative,
            'form' => $form->createView(),
        ]);
    }
}
