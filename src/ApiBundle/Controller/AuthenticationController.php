<?php


namespace ApiBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
                $image = $data['image'];
                $country = $data['country'];
                $city = $data['city'];
                $address = $data['address'];
                $zipCode = $data['zipCode'];
                $phoneNumber = $data['phoneNumber'];
                $jobTitle = $data['jobTitle'];
                $jobDescription = $data['jobDescription'];
                $spokenLanguages = $data['spokenLanguages'];

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
                    'spokenLanguages' => $spokenLanguages
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

}