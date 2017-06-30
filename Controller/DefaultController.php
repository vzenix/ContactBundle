<?php

namespace VZenix\Bundle\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use VZenix\Bundle\ContactBundle\Entity\ContactEntity;

/**
 * Default controller
 * @author Francisco Muros Espadas <paco@vzenix.es>
 */
class DefaultController extends Controller
{

    /**
     * Main page
     * 
     * @param Request $iRequest
     * @param bool|null $valid If the form was sent, its contain if the message has sent
     * @return Response
     *
     * @Get("/contacto", name="vzenix_contact")
     */
    public function indexAction(Request $iRequest, $valid = null)
    {
        $this->initializeRobotProtection($iRequest);

        $_lTwig = array(
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'landing' => false,
            'h1_title' => "Contacto",
            'h1_link' => $this->getRouter()->generate("vzenix_contact"),
            'robot' => $iRequest->getSession()->get("vzenix_num_a", 0) . " + " . $iRequest->getSession()->get("vzenix_num_b", 0),
            'messages_error' => $valid === false,
            'messages_sent' => $valid === true,
            'autocomplete' => array(
                'name' => $valid === false ? $iRequest->request->get('name', '') : '',
                'email' => $valid === false ? $iRequest->request->get('email', '') : '',
                'message' => $valid === false ? $iRequest->request->get('message', '') : ''
            )
        );

        return $this->render($this->getParameter("vzenix.contact.templates.view"), $_lTwig);
    }

    /**
     * Action
     * @Post("/contacto")
     * @return Response
     */
    public function sendFormAction(Request $iRequest)
    {
        $_sName = trim($iRequest->request->get("name", ""));
        $_sEmail = trim($iRequest->request->get("email", ""));
        $_sMessage = trim($iRequest->request->get("message", ""));

        /** @var bool is valid form */
        $_bValid = $this->checkRobotProtection($iRequest) &&
                $_sName !== "" &&
                $_sMessage !== "" &&
                filter_var($_sEmail, FILTER_VALIDATE_EMAIL);

        if (!$_bValid && $iRequest->query->get('no_response', 'false') === 'true') {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }

        if ($_bValid && $this->container->getParameter("vzenix.contact.log", false)) {
            $this->logRequest($_sName, $_sEmail, $_sMessage);
        }

        if ($_bValid && $this->container->getParameter("vzenix.contact.swiftmailer", false)) {
            $this->sendMail($_sName, $_sEmail, $_sMessage);
        }

        return $iRequest->query->get('no_response', 'false') === 'true' ?
                new Response("", Response::HTTP_NO_CONTENT) :
                $this->indexAction($iRequest, $_bValid);
    }

    /**
     * Initialize the field of form for robot protection, and check previous
     * protection is sucefully completed
     * @param Request $iRequest
     */
    private function initializeRobotProtection(Request $iRequest)
    {
        $iRequest->getSession()->set("vzenix_num_a", (int) rand(1, 9));
        $iRequest->getSession()->set("vzenix_num_b", (int) rand(1, 9));

        $iRequest->getSession()->set("vzenix_time", time());
    }

    /**
     * Check if form is not send for a machine
     * @param Request $iRequest
     * @return bool false if anti-robot test fail
     */
    private function checkRobotProtection(Request $iRequest): bool
    {
        $_nRequiredSum = ((int) $iRequest->getSession()->get("vzenix_num_a", 0)) +
                ((int) $iRequest->getSession()->get("vzenix_num_b", 0));

        $_nRequestSum = (int) $iRequest->request->get("calculator", 0);

        $_nCurrentSavedTime = (int) $iRequest->getSession()->set("vzenix_time", 0);

        return $_nRequestSum > 0 &&
                $_nRequiredSum > 0 &&
                $_nRequestSum === $_nRequiredSum &&
                (time() - $_nCurrentSavedTime) >= $this->getMinTime();
    }

    /**
     * Log into database a request
     * @param string $sName
     * @param string $sEmail
     * @param string $sMessage
     * @return ContactEntity Entry generated into database
     */
    private function logRequest(string $sName, string $sEmail, string $sMessage): ContactEntity
    {
        $_iEntity = new ContactEntity();
        $_iEntity->setName($sName);
        $_iEntity->setEmail($sEmail);
        $_iEntity->setMessage($sMessage);

        $this->getDoctrine()
                ->getManager()
                ->persist($_iEntity);

        $this->getDoctrine()
                ->getManager()
                ->flush();

        return $_iEntity;
    }

    /**
     * Send email
     * @param string $sName
     * @param string $sEmail
     * @param string $sMessage
     */
    private function sendMail(string $sName, string $sEmail, string $sMessage)
    {
        $_lTwigParam = array('name' => $sName, 'email' => $sEmail, 'message' => $sMessage);
        $_iMessage = new \Swift_Message('Hello Email');
        $_iMessage
                ->setSubject($this->getParameter("vzenix.contact.mail.subject"))
                ->setFrom($this->getParameter("vzenix.contact.mail.from"))
                ->setTo($this->getParameter("vzenix.contact.mail.to"))
                ->setBody($this->renderView('emails/contact.html.twig', $_lTwigParam), 'text/html')
        ;

        $this->get('mailer')->send($_iMessage);
    }

    /**
     * Get the min time in second betwen request
     * @return int
     */
    private function getMinTime(): int
    {
        return (int) $this->getParameter("vzenix.contact.lapsus");
    }

    /** @return \Symfony\Bundle\FrameworkBundle\Routing\Router */
    private function getRouter()
    {
        return $this->get("router");
    }

}
