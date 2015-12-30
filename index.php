<?php

  //this is design to get the xml file
	if(isset($_GET['xml'])){
	   $xml=$_GET['xml'];
	   if(@simplexml_load_file($xml)){
	  	 $Contact_XML = simplexml_load_file($xml);
	  	 
	   }else{
	   		die('<script type="text/javascript">alert("no existing xml file");</script>');
	   }
	  	
	}else{
	   $xml='';
	}
	   
	  
	// the proccess to check the data and put them in the data base only works when
	// there is an xml file or data inside the boxes
   if(isset($_POST['BtnSubmit'])or ($xml != '')) {
   
      $You_have_To_Check='';  // this is made to collect name of the errors to put it in a 
      						  // a pop up message 
      						  
      // To avoid Problems when the data is about to be set in the Data Base
      // I choose to "inicialate" them and if anything went wrong or we reload the page
      // I can make sure that everything starts from the beggining
      
      // when a xml file is used the text boxes are supposed to be blank
      if($xml == '' or $_POST['Firstname']=='' or $_POST['Lastname']=='' or $_POST['Address']==''  or $_POST['Email'] =='' or $_POST['Phone']=='' ){
      	// in this case we get the variables from the text file
	      @$Firstname=$_POST['Firstname'];
	      @$Lastname=$_POST['Lastname'];
	      @$Address=$_POST['Address'];
	      @$Email=$_POST['Email'];
    	  @$Phone=$_POST['Phone'];
	  	// create object  contact
	  	
	      $Firstname_text= $Firstname;
	  	  $Lastname_text=$Lastname;
	  	  $Address_text=$Address;
	  	  $Email_text=$Email;
	  	  $Phone_text=$Phone;
	  	
      }else{
      // in this case we get the variables from xml file
      	  
      	  @$Firstname=$Contact_XML->firstname;
	      @$Lastname=$Contact_XML->lastname;
	      @$Address=$Contact_XML->address;
	      @$Email=$Contact_XML->email;
	      @$Phone=$Contact_XML->phone;

      }	  
      
      
      $latin_words=utf8_encode("Ò—·ÈÌÛ˙¡…Õ”⁄‰ÎÔˆ¸ƒÀœ÷‹ﬂ");// encode the latin words in utf8 those are the one that I know to be able to use it in preg_match
      //to decode is  utf8_decode();
      $wordsallowed="/^[a-zA-Z-" . $latin_words . " ]*$/"; // this the string to be used where is included the characters allowed
      $wordsandnumbersallowed="/^[0-9a-zA-Z-" . $latin_words . " ]*$/"; // same as wordsallowd but with the numbers
      
      
      
      // this part check if the First name field is not empty and just have letters and theres no just space
      if(!isset($Firstname) or ($Firstname=='') or !preg_match($wordsallowed,$Firstname) or ctype_space($Firstname)){
      	$You_have_To_Check="Name ";
      	 
      }
          // this part check if the Last name field is not empty and just have letters and numbers and there is no just space
    
      if(!isset($Lastname) or ($Lastname=='') or !preg_match($wordsallowed,$Lastname)or ctype_space($Lastname)){
        $You_have_To_Check=$You_have_To_Check=$You_have_To_Check . "Last Name ";
      }
        // this part check if the Email field is not empty and it's format is correct including if the space is with just spaces
    
      if(!filter_var($Email, FILTER_VALIDATE_EMAIL)){
        $You_have_To_Check=$You_have_To_Check."Email ";
      }
      
      // the address is not mandatory, but if it is set the address is supposed to respect a format
      
      
      if((isset($Address))){
      		if(!preg_match($wordsallowed,$Address)and preg_match("/^[0-9- ]*$/",$Address)){
      			// check if the user forgot to put the name of the address
      
      		    $You_have_To_Check=$You_have_To_Check."Address(need to put the name) ";
	 		}elseif(!preg_match("/^[0-9 ]*$/",$Address)and preg_match($wordsallowed
	 		,$Address)){
	 			// check if the user forgot to put the number of the address
      	
	 			$You_have_To_Check=$You_have_To_Check."Address(need the number) ";
	 		}elseif(!preg_match($wordsandnumbersallowed,$Address)){
	 			// check if there is a symbol in the textbox that is not a letter number or a line
	 			$You_have_To_Check=$You_have_To_Check."Address(not allowed symbols) ";
	 		}
      }
         
         
         // if a there was any error in the data introduced means that the variable You_have_To_Check is not null, so 
         // a alert pop up appears showing in which block or blocks the error took place
         // if everything was ok, is time to put the data in the database
      if($You_have_To_Check != ''){
		echo '<script type="text/javascript">alert("You have to check ' . $You_have_To_Check . 'box");</script>';      
	  }else {
	  	//Ready to put in the Data Base and put the text fields in blank
	  	
	  	  $Firstname_text='';
	  	  $Lastname_text='';
	  	  $Address_text='';
	  	  $Email_text='';
	  	  $Phone_text='';

	  	//set the variables to the data base
	  
	  	// create object  contact
	  	    $Firstname_in=$Firstname;
      		$Lastname_in=$Lastname;
      		$Address_in=$Address;
      		$Email_in=$Email;
      		$Phone_in=$Phone;
      		      		
      		include("contact.php");
      		$contact_inDB = new contact($Firstname_in,$Lastname_in,$Address_in,$Email_in,$Phone_in);
      		
      		//here the database conection is stablished
      		$connection=mysql_connect("localhost","root","");
 			$DataBase=mysql_select_db("exercise",$connection);
     		//this variable is where the commands for the database are set
      		$command_wr="INSERT INTO contact (firstname, lastname, address, email, phone) VALUES ('$Firstname','$Lastname','$Address','$Email','$Phone')";
			
			// The datas are inserted
  			$hacerConsulta=mysql_query ($command_wr,$connection);
	
      		
      		// check database, with another contact objet compare datas that were in to the data's out
      		
      		$command_rd="SELECT firstname, lastname, address, email, phone FROM contact  ORDER BY contact_id DESC LIMIT 1;";
    	    $getcontact=mysql_query($command_rd, $connection);
			$contact_out = mysql_fetch_array($getcontact, MYSQL_ASSOC);
			
			$contact_outDB = new contact($contact_out["firstname"],$contact_out['lastname'],$contact_out['address'],$contact_out['email'],$contact_out['phone']);
      		if($contact_inDB == $contact_outDB){
      			echo '<script type="text/javascript">alert("Data in success");</script>';      
			}else{
 				echo '<script type="text/javascript">alert("Data not in");</script>';      
      		}
			
			
		$xml='';// this is to make sure the variable is reset to zero
	  }
        
    }
 

// then is the HTML code for the web page
?>

<h2>CONTACT</h2>
<title>Contact</title>
<form name="ContactForm" method="POST" action="#">
	<table width="350" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Enter Your First Name :</td>
        <td><textarea name="Firstname" rows="1" cols="20"><?php echo @$Firstname_text; ?></textarea></td>
      </tr>
      <tr>
        <td>Enter Your Last Name :</td>
        <td><textarea name="Lastname" rows="1" cols="20"><?php echo @$Lastname_text; ?></textarea></td>
      </tr>
      <tr>
        <td>Address :</td>
        <td><textarea name="Address" rows="1" cols="20"><?php echo @$Address_text; ?></textarea></td>
      </tr>
      <tr>
        <td>Email :</td>
        <td><textarea name="Email" rows="1" cols="20"><?php echo @$Email_text; ?></textarea></td>
      </tr>
      <tr>
        <td>Phone :</td>
        <td><textarea name="Phone" rows="1" cols="20"><?php echo @$Phone_text; ?></textarea></td>
      </tr>
     <tr>
        <td><br></td>
      </tr>

      <tr>
        <td><input name="BtnSubmit" type="submit" value="Save Contact"></td>
      </tr>      
    </table>
</form>
