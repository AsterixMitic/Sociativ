<?php

	class Image{

		public static function uploadProfileImage($formname, $userId){

			if($_FILES[$formname]['size']>5120000){
				die("Image too big, must be under 5MB!");
			}

			$filename = $_FILES[$formname]["name"];
	    	$tempname = $_FILES[$formname]["tmp_name"];   
	        $folder = "profile_images/".$filename;
	         
	        DB::query('UPDATE socialnetwork.users SET profileimg=:filename WHERE id=:userid', array(':filename'=>$filename, ':userid'=>$userId));

	        if (move_uploaded_file($_FILES[$formname]["tmp_name"], $folder))  {
	            echo "Image uploaded successfully";
	        }else{
	            echo "Failed to upload image";
	      }

		}

		public static function uploadImage($formname, $postId){

			if($_FILES[$formname]['size']>5120000){
				die("Image too big, must be under 5MB!");
			}

			$filename = $_FILES[$formname]["name"];
	    	$tempname = $_FILES[$formname]["tmp_name"];   
	        $folder = "images/".$filename;
	         
	        DB::query('UPDATE socialnetwork.posts SET postimg=:filename WHERE id=:postid', array(':filename'=>$filename, ':postid'=>$postId));

	        if (move_uploaded_file($_FILES[$formname]["tmp_name"], $folder))  {
	            echo "Image uploaded successfully";
	        }else{
	            echo "Failed to upload image";
	      }

		}

	}

?>