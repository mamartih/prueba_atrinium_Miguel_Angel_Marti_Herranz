<?php

namespace App\Controller;

use App\Entity\Historic;
use App\Form\HistoricType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Constraints\Json;

class HistoricController extends AbstractController
{
    /**
     * @Route("/historic", name="historic")
     */
    public function index(Request $request): Response
    {
        $historic = new Historic();
        $form = $this->createForm(HistoricType::class,$historic);
        //echo '<pre>'; var_dump($request); echo '</pre>'; die;
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()) {
            $em=$this->getDoctrine()->getManager();           
            $em->persist($historic);
            $em->flush();
            return $this->redirect('historic');
        }
        return $this->render('historic/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/historicSearch", name="historic-search")
     */
    public function SearchCurrency(Request $request){
        $date = $_POST['date'];
        $params=[
           'origin_currency' => $_POST['origin_currency'],
           'destiny_currency' => $_POST['destiny_currency'],
           'original_import' => $_POST['original_import'],
           'date' => date_create_from_format('Y-n-j H:i:s',$date),
        ];
        
        $em = $this->getDoctrine()->getManager();
        $historic = $em->getRepository(Historic::class)->findBy([
            'origin_currency'=>$params['origin_currency'],
            'destiny_currency'=>$params['destiny_currency'],
            'original_import'=>$params['original_import'],
            'date'=>$params['date']
            ]);
        if (count($historic)>0) {
            return $this->json($historic);
        }else{
            $params['date'] = date_create_from_format('Y-m-d H:i:s', $date);
            $dateFormat=$params['date'];
            $params['date'] = date_format($params['date'], 'Y-m-d');
            $data = $this->HistoricControllerStore($params);
            $data['time']=$dateFormat;
            $this->SaveHistoric($data);
            $response[]=$data;

            return $this->json($response);
        }

    }

    public function HistoricControllerStore($params=NULL){
        if ($params!=NULL) {
            // set API Endpoint and API key 
                $endpoint = $params['date'];
                $access_key = 'fa01835b25a6f6190a0e2eae238a0d73';
                $base = '&base='.$params['origin_currency'].'&';
                $symbols = 'symbols='.$params['destiny_currency'];

                // Initialize CURL:
                $ch = curl_init('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.$base.$symbols);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Store the data:
                $json = curl_exec($ch);
                curl_close($ch);

                // Decode JSON response:
                $exchangeRates = json_decode($json, true);

                //Calculate Change

                $params['finalImport'] = $exchangeRates['rates'][$params['destiny_currency']]*$params['original_import'];      
        }

        return $params;
    }

    public function SaveHistoric($data)
    {   
        // Initialize Historic:
        $historic = new Historic();
        $historic->setAll($data);

        // Persist Historic:
        $em=$this->getDoctrine()->getManager();
        $em->persist($historic);
        $em->flush();

        return;
    }

    /**
     * @Route("/historicdestinycurrency", name="historic-destiny-currency")
     */
    public function HistoricDestinyCurrency(){
        // Initialize CURL:
        $ch = curl_init('http://data.fixer.io/api/latest?access_key=fa01835b25a6f6190a0e2eae238a0d73&format=1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $exchangeRates = json_decode($json, true);

        // Access the exchange rate values, e.g. GBP:
        $rates = $exchangeRates['rates'];

        return $this->json($rates);
    }

}
