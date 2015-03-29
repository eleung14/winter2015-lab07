<?php


class Order extends CI_Model 
{
      // Constructor
    public function __construct() {
        
        parent::__construct();
        $this->load->model('Menu');
   }
   
   public function get_order($filename)
   {

        $xml = simplexml_load_file(DATAPATH . $filename.'.xml');
               $order = array();
       $order['order_name'] = $filename;
       $order['customer'] = (String)$xml->customer;
       $order['order_type'] = (String)$xml->attributes()['type'];


    $order['special'] = (String)$xml->special;
           
           return $order;
   }
   
   
    public function get_burgers($filename)
    {
       $xml = simplexml_load_file(DATAPATH . $filename.'.xml');
            
       
       //Variables
       $total = 0;
       $numOfBurgers = 0;

       

       //Getting all the burgers
       $burgers = array();

       foreach ($xml->burger as $burger)
       {
           $subtotal = 0;
           $oneBurger = array();
           
           $numOfBurgers++;          
           $oneBurger['burger_number'] = $numOfBurgers;

            $oneBurger['instructions'] = (String)$xml->attributes['instructions'];

           //Set patty
           $oneBurger['patty'] = $this->get_patty($burger);


            $oneBurger['cheeses'] = $this->get_cheeses($burger);

            $oneBurger["name"] = $this->get_name($burger);

            $oneBurger["toppings"] = $this->get_toppings($burger);

            $oneBurger["sauces"] = $this->get_sauces($burger);  

            $oneBurger["subtotal"] = $this->get_subtotal($burger);

        
            $total += $oneBurger["subtotal"];
            
           
            array_push($burgers,$oneBurger);
        }
        
        //store the total
        $burgers["total"] = $total;
        
        return $burgers;
    }
     
    
    
    
    
    
    /**
     * PRIVATE FUNCTIONS!!!    
     */
    
    
    private function get_patty($burger)
    {     
       return $burger->patty["type"];        
    }
      
    
    private function get_cheeses($burger)
    {
        $return_string = '';
        
        //Check if top is set
        if(isset($burger->cheeses["top"]))
        {
            $return_string = $return_string." $burger->cheeses['top'] (top)";                 
        }              
        
        //Check if they wanted cheese
        if(isset($burger->cheeses["bottom"]))            
        {
            $return_string = $return_string." $burger->cheeses['bottom'] (bottom)";         
            return $return_string;
        }
        //Returns 'none' if not set
        return 'none';
    }
    
    private function get_instruction($burger)
    {
        if(isset($burger->instruction))
            return $burger->instruction;
        //Returns 'none' if not set
        return 'none';
    }
    
    private function get_name($burger)
    {
        if(isset($burger->name))
            return $burger->name;
        //Returns 'none' if not set
        return 'none';
    }
    
    private function get_toppings($burger)
    { 
        $return_string = '';
        if(isset($burger->topping))
        {
            foreach ($burger->topping as $topping)
            {
                $return_string .= (string)$topping['type'].", ";
            } 

            return substr($return_string, 0, strlen($return_string) - 2);   
        }
         
        return 'none';                    
    }
    
    
    private function get_sauces($burger)
    {
       $return_string = '';
        if(isset($burger->sauce))
        {
            foreach ($burger->sauce as $sauce)
            {
                $return_string .= (string)$sauce['type'].", ";       
            } 
            return substr($return_string, 0, strlen($return_string) - 2);
        }         
        return 'none';                               
    }
    
    private function get_subtotal($burger)
    {
        //Things that have prices:
        //  <patties> <cheeses> <toppings> <sauces>
        $subtotal = 0;
        
        if($this->get_patty($burger) != 'none')
            $subtotal += $this->Menu->getPatty((string)$burger->patty['type'])->price;
        
        if(isset($burger->cheeses["top"]))
            $subtotal += $this->Menu->getCheese((string)$burger->cheeses['top'])->price;
        
        if(isset($burger->cheeses["bottom"])) 
            $subtotal += $this->Menu->getCheese((string)$burger->cheeses['bottom'])->price;

        //Check if they wanted cheese
        if(isset($burger->cheeses["bottom"]))    
      
        if($this->get_toppings($burger) != 'none')
        {
            foreach($burger->toppings as $topping)
                $subtotal += $this->Menu->getTopping((string)$topping);
        }
        
        if($this->get_sauces($burger) != 'none')
        {
            foreach($burger->sauces as $sauce)
                $subtotal += $this->Menu->getSauce((string)$sauce);
        }
        
        return $subtotal;
    }
}
