<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Contact;

class ContactController extends Controller
{
    /**
     * @Route("/contacts", name="contacts_list")
     * @Method({"GET"})
     */
    public function getContactsAction(Request $request)
    {
        $contacts = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Contact')
                ->findAll();
        /* @var $contacts Contact[] */

        $formatted = [];
        foreach ($contacts as $contact) {
            $formatted[] = [
               'id' => $contact->getId(),
               'name' => $contact->getName(),
               'surname' => $contact->getSurname(),
               'mail' => $contact->getMail(),
               'phoneNumber' => $contact->getPhoneNumber()
            ];
        }

        return new JsonResponse($formatted);
    }

    /**
     * @Route("/contacts/{search}", name="contact_search")
     * @Method({"GET"})
     */
    public function getContactAction(Request $request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Contact');

        $query = $repository->createQueryBuilder('p')
            ->where('p.id = :search')
            ->orWhere('p.name = :search')
            ->orWhere('p.surname = :search')
            ->orWhere('p.phoneNumber = :search')
            ->orWhere('p.mail = :search')
            ->setParameter('search', $request->get('search'))
            ->getQuery();

        $contacts = $query->getResult();

        if(empty($contacts)){
            return new JsonResponse(['message' => 'Contact inexistant'], Response::HTTP_NOT_FOUND);
        }
        $formatted = [];
        foreach ($contacts as $contact) {
            $formatted[] = [
               'id' => $contact->getId(),
               'name' => $contact->getName(),
               'surname' => $contact->getSurname(),
               'mail' => $contact->getMail(),
               'phoneNumber' => $contact->getPhoneNumber()
            ];
        }

        return new JsonResponse($formatted);
    }


    /**
     * @Route("/contacts", name="contact_add")
     * @Method({"POST"})
     */
    public function postContactAction(Request $request) {
        $contactData = json_decode($request->getContent(), true);
        $contact = new Contact($contactData);

        $em = $this->getDoctrine()->getManager();
        $em->persist($contact);
        $em->flush();

        return new JsonResponse(['message' => 'contact enregistrer avec l\'id : '.$contact->getId()], Response::HTTP_CREATED);
    }

    /**
     * @Route("/contacts/{id}", name="contacts_delete")
     * @Method({"DELETE"})
     */
    public function removeContactAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $contact = $em->getRepository('AppBundle:Contact')
                       ->find($request->get('id'));
        /* @var $contacts Contact[] */

        if($contact) {
            $em->remove($contact);
            $em->flush();
        }

        return new Response(Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/contacts/{id}", name="contacts_update")
     * @Method({"PUT"})
     */
    public function updateContactAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $contactOld = $em->getRepository('AppBundle:Contact')
                       ->find($request->get('id'));
        /* @var $contacts Contact[] */


        if(empty($contactOld)) {
            return new JsonResponse(['message' => 'Contact inexistant'], Response::HTTP_NOT_FOUND);
        }

        $contactData = json_decode($request->getContent(), true);
        $contact = new Contact($contactData);
        $contact->setId($contactOld->getId());

        $em->merge($contact);
        $em->flush();

        return new Response(Response::HTTP_NO_CONTENT);
    }
}