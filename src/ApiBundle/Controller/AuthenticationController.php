<?php


namespace ApiBundle\Controller;


use ApiBundle\Entity\AccountLink;
use ApiBundle\Entity\File;
use ApiBundle\Entity\StripeConnectAccount;
use ApiBundle\Entity\Zzeend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use UserBundle\Entity\User;

class AuthenticationController extends Controller
{

    public function signUpAction(Request $request)
    {
        $response = array();
        $data = $request->getContent();
        $data = json_decode($data, true);

        if(count($data) > 0) {

            if(isset($data['email']) AND isset($data['password']) AND isset($data['fullname']) AND isset($data['accountType']) AND isset($data['image']) AND isset($data['country']) AND isset($data['city']) AND isset($data['address']) AND isset($data['zipCode'])AND isset($data['phoneNumber'])AND isset($data['jobTitle'])AND isset($data['jobDescription'])) {
                $email = $data['email'];
                $password = $data['password'];
                $fullname = $data['fullname'];
                $accountType = $data['accountType'];
                $image = null;
                $country = $data['country'];
                $city = $data['city'];
                $address = $data['address'];
                $zipCode = $data['zipCode'];
                $phoneNumber = $data['phoneNumber'];
                $jobTitle = $data['jobTitle'];
                $jobDescription = $data['jobDescription'];
                $spokenLanguages = $data['spokenLanguages'];
                $subLocality = $data['subLocality'];
                $latitude = $data['latitude'];
                $longitude = $data['longitude'];
                $subAdministrativeArea = $data['subAdministrativeArea'];
                $administrativeArea = $data['administrativeArea'];
                $countryCode = $data['countryCode'];

                $response = $this->forward("UserBundle:User:newUser", [
                    'email' => $email,
                    'password' => $password,
                    'fullname' => $fullname,
                    'accountType' => $accountType,
                    'image' => $image,
                    'country' => $country,
                    'city' => $city,
                    'address' => $address,
                    'zipCode' => $zipCode,
                    'phoneNumber' => $phoneNumber,
                    'jobTitle' => $jobTitle,
                    'jobDescription' => $jobDescription,
                    'spokenLanguages' => $spokenLanguages,
                    'subLocality' => $subLocality,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'subAdministrativeArea' => $subAdministrativeArea,
                    'administrativeArea' => $administrativeArea,
                    'countryCode' => $countryCode,
                ]);
            }else{
                $response = array('code' => 'auth/imcomplete_data');
                return new JsonResponse($response);
            }

        }else{
            $response = array('code' => 'auth/empty_data');
            return new JsonResponse($response);
        }

        return $response;
    }

    public function sendVerificationMailAction(Request $request){
        $data = $request->getContent();
        $data = json_decode($data, true);

        $email = $data['email'];
        return $this->forward("UserBundle:User:sendVerificationMail", [
            'email' => $email
        ]);
    }

    public function enableAccountAction($codeGen){
        return $this->forward("UserBundle:User:enableAccount", [
            'codeGen' => $codeGen
        ]);
    }

    public function sendPasswordForgotMailAction(Request $request){
        $data = $request->getContent();
        $data = json_decode($data, true);

        $email = $data['email'];
        return $this->forward("UserBundle:User:sendPasswordForgotMail", [
            'email' => $email
        ]);
    }

    public function resetPasswordAction(Request $request){
        $data = $request->getContent();
        $data = json_decode($data, true);

        $codeGen = $data['codeGen'];
        $newPassword = $data['newPassword'];
        return $this->forward("UserBundle:User:resetPassword", [
            'codeGen' => $codeGen,
            'newPassword' => $newPassword
        ]);
    }

    public function resetPasswordRenderAction(){

        return $this->forward("WebBundle:Web:resetPasswordRender", [
        ]);
    }

    public function getFileAction($fileType, $fileName){

        $uploadDir = $this->getParameter('upload_dir');
        $filepath = '';

        // if the asked file is profile photo
        if($fileType == 'VPA4iST9YEAk0CiawXEKbcArkfDSBKg5Re9gfywvmK'){

            $filepath = $uploadDir.'/profile_photos/'.$fileName;

        }

        //if the asked file is a post photo
        if($fileType == 'fBfqcChzEM9ai3hQvX0GC80KibabT1uU6LXtSYqpn1'){

            $filepath = $uploadDir.'/post_photos/'.$fileName;

        }

        //if the asked file is a post video
        if($fileType == 'fBfqcChzEM9ai3hQvX0GC80KibabT1uU6LXtSYqpn1ZC3653sndkxn22e0'){

            $filepath = $uploadDir.'/post_videos/'.$fileName;

        }

        //if the asked file is a chat photo
        if($fileType == 'cfBfqcChzEM9ai3hQvX0GaC80KibabT1uUdf6LXtSYqpn1h'){

            $filepath = $uploadDir.'/chat_photos/'.$fileName;

        }

        //if the asked file is a chat photo
        if($fileType == 'afBfqcChzEM9ai3hdQvX0GC80KibabT1uU6LviXtSYqpn1ZdeoC3653sndkxn22e01996'){

            $filepath = $uploadDir.'/chat_videos/'.$fileName;

        }

        //if the asked file is a story photo
        if($fileType == 'fBfqcChzEM9arevi3hQvX0GC80stybabT1uU6LXtSYqpn10934'){

            $filepath = $uploadDir.'/story_photos/'.$fileName;
//           return new JsonResponse($filepath);

        }

        //if the asked file is a story video
        if($fileType == 'tyfBfqcChzEM9ar38sudmlevi3hQvX0GC80stybasbT1uU6LXtSYqpn10934'){

            $filepath = $uploadDir.'/story_videos/'.$fileName;
//           return new JsonResponse($filepath);

        }

        //expose image file to the web

        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $fileName);
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->setContent(file_get_contents($filepath));

        return $response;

    }

    public function addPhotoAction(Request $request){


        if (!empty($request->files->get('profilePhoto'))) {

            $response = array();

            $fileOriginalName = '';

            $fileSize = 0;

            $file = $request->files->get('profilePhoto');

            $uploadDir = $this->getParameter('upload_dir');

            $data = json_decode($_POST['data'], true);

            $dataType = $data['dataType'];

            $fileName = $this->get('ionicapi.fileUploaderManager')->upload($file, $uploadDir, $dataType);

            $fileOriginalName = $file->getClientOriginalName();

            $fileSize = $file->getClientSize();

            $data = $data['objectData'];
            $relatedId = $data['relatedId'];

            if($fileName !== ''){


                $currentUser = $this->getDoctrine()->getRepository(User::class)->find($relatedId);

                if($currentUser !== null){

                    $fileEntityManager = $this->getDoctrine()->getManager();

                    $file = new File();
                    $file->setUser($currentUser);
                    $file->setFilePath('VPA4iST9YEAk0CiawXEKbcArkfDSBKg5Re9gfywvmK/' . $fileName);
                    $file->setFileType('image');
                    $file->setFileSize($fileSize);
                    $file->setThumbnail('');
                    $file->setFileName($fileOriginalName);
                    $file->setCreatedAtAutomatically();

                    $fileEntityManager->persist($file);
                    $fileEntityManager->flush();

                    $currentUser->setPhoto($file);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($currentUser);
                    $entityManager->flush();

                }

            }


        }else{

            $response = array("code" => "action_not_allowed");

        }

        return new JsonResponse($response);

    }

    public function refreshAccountLinkAction($refreshToken){

        $accountLink = $this->getDoctrine()->getRepository(AccountLink::class)->findOneBy(['refreshToken' => $refreshToken]);

        if($accountLink !== null){
            $user = $accountLink->getUser();

            $stripeConnectAccount = $this->getDoctrine()->getRepository(StripeConnectAccount::class)->findOneBy(['user'=>$user]);

            if($stripeConnectAccount !== null){
                      $stripeAccountId = $stripeConnectAccount->getStripeAccountId();

                $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];

                \Stripe\Stripe::setApiKey($stripeSecretKey);

                //generate a codeGen for email verification
                $refreshToken = $this->get('ionicapi.tokenGeneratorManager')->createToken();
                $returnToken = $this->get('ionicapi.tokenGeneratorManager')->createToken();

                $baseUrl = $this->getParameter('baseUrl');

                $refreshUrl = $baseUrl.'/auth/refresh-link/'.$refreshToken;
                $returnUrl = $baseUrl.'/auth/return-link/'.$returnToken;

                $account_links = \Stripe\AccountLink::create([
                    'account' => $stripeAccountId,
                    'refresh_url' => $refreshUrl,
                    'return_url' => $returnUrl,
                    'type' => 'account_onboarding',
                ]);

                if($account_links !== null){

                    $accountLink = new AccountLink();

                    $entityManager = $this->getDoctrine()->getManager();

                    $accountLink->setUser($user);
                    $accountLink->setRefreshToken($refreshToken);
                    $accountLink->setReturnToken($returnToken);
                    $accountLink->setCreatedAtAutomatically();

                    $entityManager->persist($accountLink);
                    $entityManager->flush();

                    //redirect user to the new like generated
                    return $this->redirect($account_links->url);


                }else{
                    $response = array("code" => "error_occurred");
                }


            }else{
                $response = array("code" => "action_not_allowed");
            }
        }else{
            $response = array("code" => "auth/no_refresh_token_given");
        }

        return new JsonResponse($response);
    }

    public function returnAccountLinkAction($returnToken){

        $accountLink = $this->getDoctrine()->getRepository(AccountLink::class)->findOneBy(['returnToken' => $returnToken]);

        if($accountLink !== null) {
            $user = $accountLink->getUser();

            $stripeConnectAccount = $this->getDoctrine()->getRepository(StripeConnectAccount::class)->findOneBy(['user' => $user]);

            if ($stripeConnectAccount !== null) {
                $stripeAccountId = $stripeConnectAccount->getStripeAccountId();

                $stripeSecretKey = $this->getParameter('api_keys')['stripe-secret-key'];

                $stripe = new \Stripe\StripeClient($stripeSecretKey);
                $connectAccount = $stripe->accounts->retrieve(
                    $stripeAccountId,
                    []
                );

                $charges_enabled = $connectAccount->charges_enabled;

                $external_accounts_data = $connectAccount->external_accounts['data'];

                if(count($connectAccount->capabilities) > 0 AND $connectAccount->capabilities['card_payments'] == true AND $connectAccount->capabilities['transfers'] == true AND$charges_enabled == true AND count($external_accounts_data) > 0 ){

                    $stripeConnectAccount->setActiveAutomatically();

                    $entityManager = $this->getDoctrine()->getManager();

                    $entityManager->persist($stripeConnectAccount);
                    $entityManager->flush();

                    return new JsonResponse("You can now close this page and continue using zZeend, Congratulation !!!");
                }else{
                    return new JsonResponse("Uncompleted onboarding");
                }


                return new JsonResponse($connectAccount);
            }

        }else{
            return new JsonResponse(array("code" => "auth/no_return_token_given"));
        }

    }

    public function payoutAction($zZeendId){

        $response = array();

        $zZeend = $this->getDoctrine()->getRepository(Zzeend::class)->find($zZeendId);
        if ($zZeend) {

            $entityManager = $this->getDoctrine()->getManager();

            $zZeend->setPayoutAutomatically();

            $entityManager->persist($zZeend);
            $entityManager->flush();

            $mainZzeendUser = $zZeend->getUser();
            $subject = 'You have been payout into your bank account.';
            //send notification
            $pushNotificationManager = $this->get('ionicapi.push.notification.manager');
            $data = array("type" => 18,
                "zZeend" => $zZeend);
            $pushNotificationManager->sendNotification($mainZzeendUser, 'zZeend payout (n° '.$zZeend->getId().')', $subject, $data, null);

            $response = array("code" => "zZeend_payout_success");

        }

        return new JsonResponse($response);
    }

}