<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Booking;
use AppBundle\Form\BookingType;
use AppBundle\Form\TicketsType;
use AppBundle\Manager\BookingManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BookingController extends Controller
{

    /**
     * @Method({"GET","POST"})
     * @param Request $request
     * @param BookingManager $bookingManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/", name="accueil")
     */
    public function indexAction(Request $request, BookingManager $bookingManager)
    {

        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingManager->bookingComplete($booking);
            $bookingManager->setBookingSession($booking);

            return $this->redirectToRoute('ticket');
        }
        return $this->render('booking/index.html.twig', ['form' => $form->createView()]);


    }


    /**
     * @Method({"GET","POST"})
     * @param Request $request
     * @param BookingManager $bookingManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/ticket", name="ticket")
     */
    public function ticketAction(Request $request, BookingManager $bookingManager)
    {
        $booking = $request->getSession()->get('booking');
        $bookingManager->bookingComplete($booking);

        $form = $this->createForm(TicketsType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingManager->getPriceOfTicket($booking);
            $bookingManager->setBookingSession($booking);

            // echo '<pre>', var_dump($booking->getTickets()); die;

            return $this->redirectToRoute('summary');
        }
        return $this->render('booking/ticket.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Method({"GET","POST"})
     * @param Request $request
     * @param BookingManager $bookingManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/summary", name="summary")
     */
    public function summaryAction(Request $request, BookingManager $bookingManager)
    {
        $booking = $bookingManager->getBookingSession();

        if ($request->getMethod() === Request::METHOD_POST) {
            if ($bookingManager->payStep($request, $booking)) {
                $this->addFlash('success', [
                    'id' => 'payment_message.success',
                    'parameters' => ['%email%' => $booking->getEmail()]
                ]);
                return $this->redirectToRoute('final_summary');
            } else {
                $this->addFlash('error', 'payment.message.error');
            }
        }
        return $this->render('booking/summary.html.twig', ['booking' => $booking]);
    }

    /**
     * @Method({"GET","POST"})
     * @param BookingManager $bookingManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/final_summary", name="final_summary")
     */
    public function finalSummaryAction(BookingManager $bookingManager)
    {
        $booking = $bookingManager->getBookingSession();
        $bookingManager->clearSession();

        return $this->render('booking/final_summary.html.twig', ['booking' => $booking]);
    }

}