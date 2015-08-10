<?php
    
    require_once 'aws_signed_request.php';
    class AmazonProductAPI
    {
        /** Your Amazon Access Key Id */
        private $public_key     = "AKIAIYU6HDGPALZNUCDQ";
        
        /**
         * Your Amazon Secret Access Key
         * @access private
         * @var string
         */
        private $private_key    = "2rk6UHL66Caco/5bdDekK43FAeJQ+rpeBWpJjFTZ";
        
        /** Your Amazon Associate Tag */
        private $associate_tag  = "vincentrbello-20";
    
        /**
         * Constants for product types
         * @access public
         * @var string
            http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/APPNDX_SearchIndexValues.html
        */
        const VIDEO = "Video";
        
        
        /**
         * Check if the xml received from Amazon is valid
         * 
         * @param mixed $response xml response to check
         * @return bool false if the xml is invalid
         * @return mixed the xml response if it is valid
         * @return exception if we could not connect to Amazon
         */
        private function verifyXmlResponse($response)
        {
            if ($response === False)
            {
                throw new Exception("Could not connect to Amazon");
            }
            else
            {
                if (isset($response->Items->Item->ItemAttributes->Title))
                {
                    return ($response);
                }
                else
                {
                    throw new Exception("");
                }
            }
        }
        
        
        /**
         * Query Amazon with the issued parameters
         * 
         * @param array $parameters parameters to query around
         * @return simpleXmlObject xml query response
         */
        private function queryAmazon($parameters)
        {
            return aws_signed_request("com", $parameters, $this->public_key, $this->private_key, $this->associate_tag);
        }
        
        
        /**
         * Return details of products searched by various types
         * 
         * @param string $search search term
         * @param string $category search category         
         * @param string $searchType type of search
         * @return mixed simpleXML object
         */
        public function searchProducts($search, $category, $searchType = "UPC")
        {
            $allowedTypes = array("TITLE", "ASIN");
            $allowedCategories = array("Video");
            
            if ($searchType == "TITLE") {
                $parameters = array("Operation"     => "ItemSearch",
                                                    "Title"         => $search,
                                                    "SearchIndex"   => $category,
                                                    "ResponseGroup" => "Large",                                                    
                                                    );            
            } else if ($searchType == "ASIN") {
                $parameters = array("Operation"     => "ItemLookup",
                                                    "ItemId"        => $search,
                                                    "IdType"        => "ASIN",
                                                    "ResponseGroup" => "Large");

            }


            $xml_response = $this->queryAmazon($parameters);
            
            return $this->verifyXmlResponse($xml_response);

        }        
    }

?>
