<?php
namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use AppBundle\Entity\Post;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AppBundle\Entity\Product;

use OpenCafe\Datium;
use DateTime;


class ProductController extends Controller{
     
private $Pdrow;
public function fetch_data_name($rows){
     $dat = ' ';
     for($r=0;$r<$rows;$r++){
       $dat .= $this->Pdrow[$r]->getId()." ".$this->Pdrow[$r]->getName()." ".$this->Pdrow[$r]->getPrice()." ".$this->Pdrow[$r]->getDescription()."<br>";
     }
    return $dat;
   }

/**
  * @Route("/addpro/{name}/{prc}/{desc}")
*/
public function createAction($name,$prc,$desc)
 {
    $product = new Product();
    $product->setName($name);
    $product->setPrice($prc);
    $product->setDescription($desc);
   
    $dt = new DateTime('now');
    $product->setCreated($dt);
    $product->setUpdated($dt);
    
    $em = $this->getDoctrine()->getManager();

    // tells Doctrine you want to (eventually) save the Product (no queries yet)
    $em->persist($product);

    // actually executes the queries (i.e. the INSERT query)
    $em->flush();
  
       return new Response('Saved new product with id '.$product->getId());
 }
 
 /**
  @Route("/create/product")
*/
public function precreate(){
  
  return $this->render('products/create_form.html.twig');
}

/**
* @Route("/create/new/product")
*/
public function CreateProd(Request $request)
{

}

/**
  @Route("/newaddproduct")
*/
public function InsertPD(Request $request){
  
  /* Received from form create_form.html.twig */

       $addPro = new Product();
   
       $rec_post['name'] = $request->request->get('name');//Received post name

         $addPro->setName($rec_post['name']);//Insert to field name
      
      $rec_post['price'] = $request->request->get('price');//Received post price

         $addPro->setPrice($rec_post['price']);//Insert to field price

      $rec_post['decscrpt'] = $request->request->get('decscrpt');//Received post decscrpt
         
          $addPro->setDescription($rec_post['decscrpt']);//Insert to field description
          
         //return new Response(var_dump($rec_post));
       
       $addPro->setCreated(new DateTime());
       $addPro->setUpdated(new DateTime());
    
           $act = $this->getDoctrine()->getManager();
           $act->persist($addPro);
           $act->flush();

   return $this->render('products/create_form.html.twig',['Data'=>$rec_post,'Id'=>$addPro->getId()]);

}

/**
* @Route("/listProdall")
*/
public function ListProductsAll(){
       $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
     // find *all* products
       $products = $repository->findAll();
    //return new Response(var_export($products));
     return $this->render('products/listprodall.html.twig',['Prods'=>$products,'TitleContain'=>'List All Products']); 

}

/**
* @Route("/plist/{item}")
*/
public function EachProduct($item)
{
   return new Response($item);

   //return $this->render('products/listprodall.html.twig',['Id'=>$item]);
}


/**
* @Route("/edit/prod")
*/
public function InstNewProd(Request $request){
   
     return new Response(var_export($request->request));

}




/**
 * @Route("/getpro/{productId}")
*/

public function showAction($productId)
{
  //query for a single product by its primary key (usually "id")
  $product = $this->getDoctrine()
        ->getRepository('AppBundle:Product')
        ->find($productId);

    if (!$product) {
        throw $this->createNotFoundException(
            'No product found for id '.$productId
        );
    }

    // ... do something, like pass the $product object into a template
    return new Response('Product ID: '.$productId." name: ".$product->getName()." price: ".$product->getPrice()." Description: ".$product->getDescription());
}

  /**
    * @Route("/showall")
  */
  
  public function showAll()
   {
     
     $repository = $this->getDoctrine()
        ->getRepository('AppBundle:Product');
      // find *all* products
       $products = $repository->findAll();
       $rows = sizeof($products);
       
       $this->Pdrow = $products; 
       $return_lnk = '<a href="/"><button>Home</button></a>';
       
       return new Response($this->fetch_data_name($rows)." ".$return_lnk);

   }
   
  

  /**
    * @Route("/showbyname/{name}")
  */
public function showByName($name){
   
   $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
   // dynamic method names to find a single product based on a column value
   $product = $repository->findOneByName($name);
     if (!$product) {
        throw $this->createNotFoundException(
            'No product found for Name '.$name
        );
    }
  
  return new Response("ID: ".$product->getId().'<br> Name: '.$product->getName().'<br> Price: '.$product->getPrice().'<br> Description: '.$product->getDescription());
  
}
  /**
    * @Route("/showbyprice/{prc}")
  */
public function showByPrice($prc){
   
   $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
   // dynamic method names to find a single product based on a column value
   $product = $repository->findOneByPrice($prc);
     if (!$product) {
        throw $this->createNotFoundException(
            'No product found for Name '.$prc
        );
    }
 return new Response("ID: ".$product->getId().'<br> Name: '.$product->getName().'<br> Price: '.$product->getPrice().'<br> Description: '.$product->getDescription());
  
}
/**
 @Route("/match-name-price/{name}/{price}")
*/
public function showby_match_NamePrice($name,$price){
  
  $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
    // query for a single product matching the given name and price
   $product = $repository->findOneBy(
    array('name' => $name, 'price' => $price)
   );
    if (!$product) {
        throw $this->createNotFoundException(
            'No product found for Name '.$name." price ".$price
        );
    }
    return new Response("ID: ".$product->getId().'<br> Name: '.$product->getName().'<br> Price: '.$product->getPrice().'<br> Description: '.$product->getDescription());
 } 

/**
 @Route("/matchname-orderprice/{name}/{sortby}")
*/
public function showby_matchName_OrderbyPrice($name,$sortby){
  $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
  // query for multiple products matching the given name, ordered by price
  //$sortby = ASC meaning min->max, or DESC max->min 
  $products = $repository->findBy(
    array('name' => $name),
    array('price' => $sortby)
  );
  if (!$products) {
        throw $this->createNotFoundException(
            'No product found for Name '.$name." Price Orderby ".$sortby
        );
    }
   
    $this->Pdrow = $products; 
    $rows = count($this->Pdrow);

  return new Response($this->fetch_data_name($rows));
}

/**
 @Route("/updateprice/{pid}/{prc}")
*/

public function updatePrice($pid,$prc){
  
  $em = $this->getDoctrine()->getManager();
    
    $product = $em->getRepository('AppBundle:Product')->find($pid);

    if (!$product) {
        throw $this->createNotFoundException(
            'No product found for id '.$pid
        );
    }
  
   $p_id = $product->getId();
   $pname = $product->getName();
   $pprice = $product->getPrice();
   
   $before = "Previous price: ".$p_id." ".$pname." ".$pprice."<br>";

   $product->setPrice($prc);
    $em->flush();
   
    $update =   $em->getRepository('AppBundle:Product')->find($pid);
   
   $p_id = $update->getId();
   $pname = $update->getName();
   $pprice = $update->getPrice(); 

    $after = "Current price: ".$p_id." ".$pname." ".$pprice;
    
    return new Response($before.$after);
   
}

/**
  @Route("/deleteproduct/{id}")
*/

public function deleteAction($id){
  
  $em = $this->getDoctrine()->getManager();
    
    $product = $em->getRepository('AppBundle:Product')->find($id);

    if (!$product) {
        throw $this->createNotFoundException(
            'No product found for id '.$id
        );
    }

 $before_delete = $product->getId()." ".$product->getName()." ".$product->getPrice()." ".$product->getDescription();

     $em->remove($product);
      $em->flush();
  
   return new Response("Before delete: ".$before_delete);

}

/**
  @Route("/queryprice/dql/{pri}")
*/
public function query_DQL($pri){

$em = $this->getDoctrine()->getManager();

$sql = 'SELECT p
    FROM AppBundle:Product p
    WHERE p.price > :price
    ORDER BY p.price ASC';
   
$query = $em->createQuery($sql)->setParameter('price', $pri);

    $products = $query->getResult();

    $data = ' ';
    for($i=0;$i<count($products);$i++){
     $data .= $products[$i]->getName()." ".$products[$i]->getPrice()." ".$products[$i]->getDescription()."<br>";
    }
  return new Response("Product Lists<br>".$data);
}




}//End class 