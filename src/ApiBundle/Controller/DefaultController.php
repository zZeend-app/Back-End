<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
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

                $response = $this->forward("UserBundle:Default:newUser", [
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
                    'jobDescription' => $jobDescription
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

    public function sendVerificationmail($email){
        return $this->forward("UserBundle:Default:sendVerificationmail", [
            'email' => $email
        ]);
    }

}
