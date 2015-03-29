<?php

/**
 * Our homepage. Show the most recently added quote.
 * 
 * controllers/Welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Welcome extends Application {

    function __construct()
    {
	parent::__construct();
    }

    //-------------------------------------------------------------
    //  Homepage: show a list of the orders on file
    //-------------------------------------------------------------

    function index()
    {
	// Build a list of orders
	
	// Present the list to choose from
	$this->data['pagebody'] = 'homepage';
        $this->load->helper('directory');
        $map = directory_map('./data/');
        $order_list = array();
        
        foreach($map as $mapvalue)            
        {
            if(substr_compare($mapvalue, '.xml', strlen($mapvalue)-strlen('.xml'), strlen('.xml'))=== 0)
            {
                if(substr_compare($mapvalue, 'order',0, 4)=== 0)
                {
                    $single_order = array('filename' => substr($mapvalue,0,6));
                    array_push($order_list, $single_order);
                }
            }  
        }
        
        
      $this->data['orders'] = $order_list;      
        

       $this->data['show_orders'] = $this->parser->parse('_order_list', $this->data, true);
       
	$this->render();
    }
    
    //-------------------------------------------------------------
    //  Show the "receipt" for a specific order
    //-------------------------------------------------------------

    function order($filename)
    {
	// Build a receipt for the chosen order
	
	// Present the list to choose from
        
        $this->load->model('Menu');
      
        $this->load->model('Order');                        
        
	$this->data['pagebody'] = 'justone';
                       
        $order  = $this->Order->get_order($filename);
          
        $burgers = $this->Order->get_burgers($filename);    

        $this->data['order_name'] = "".$order['order_name'];
        $this->data['order_type'] = "".$order['order_type'];
        $this->data['customer']   = "".$order['customer'];

        
        //Data dealing with burgers
        $this->data['burgers']    =    $burgers;
        
        $this->data['price'] = $burgers['total'];

        
     

      
      
	$this->render();
    }
    

}
