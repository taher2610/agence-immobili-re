<?php
namespace App\Notification ;

use App\Entity\contact;
use Twig\Environment;
use Symfony\Bundle\MonologBundle\SwiftMailer ;
Use symfony\mailer ;


class ContactNotification{

    public function __construct(\Swift_Mailer $mailer,Environment $renderer){
        $this->mailer =$mailer;
        $this->renderer = $renderer;
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer(): \Swift_Mailer
    {
        return $this->mailer;
    }

    /**
     * @param \Swift_Mailer $mailer
     * @return ContactNotification
     */
    public function setMailer(\Swift_Mailer $mailer): ContactNotification
    {
        $this->mailer = $mailer;
        return $this;
    }

    /**
     * @return Environment
     */
    public function getRenderer(): Environment
    {
        return $this->renderer;
    }

    /**
     * @param Environment $renderer
     * @return ContactNotification
     */
    public function setRenderer(Environment $renderer): ContactNotification
    {
        $this->renderer = $renderer;
        return $this;
    }




    public function notify(contact $contact){

        $message=(new \Swift_Message('agence :' .$contact->getProperty()->getTitle() ))
            ->setFrom('noreply@server.com')
            ->setTo('contact@agence.fr')
            ->setReplyTo($contact->getEmail())
            ->setBody($this->renderer->render('emails/contact.html.twig',[
                'contact'=>$contact
            ]),'text/html');
        $this->mailer->send($message);
        ;
    }

}