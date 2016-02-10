<?php
abstract class Account
{
	//This returns true if the account can upload files for a course
	abstract public function canUpload();

	//This returns true if the account can change accountType of other users for a course
	abstract public function canPromote();

	//This returns true if the account can delete files for a course
	abstract public function canDelete();

	//This returns an Account object with particular permissions given a certain accountType
	public static function factory( $accountType )
	{
		if ( Instructor::getName() === $accountType )
		{
			return new Instructor();
		}

		if ( Uploader::getName() === $accountType )
		{
			return new Uploader();
		}

		return new Student();
	}
}

class Instructor extends Account
{
	public function canUpload()
	{
		return TRUE;
	}

	public function canPromote()
	{
		return TRUE;
	}

	public function canDelete()
	{
		return TRUE;
	}

	public static function getName()
	{
		return "instructor";
	}
}

class Uploader extends Account
{
	public function canUpload()
	{
		return TRUE;
	}

	public function canPromote()
	{
		return FALSE;
	}

	public function canDelete()
	{
		return FALSE;
	}

	public static function getName()
	{
		return "uploader";
	}
}

class Student extends Account
{
	public function canUpload()
	{
		return FALSE;
	}

	public function canPromote()
	{
		return FALSE;
	}

	public function canDelete()
	{
		return FALSE;
	}

	public static function getName()
	{
		return "student";
	}
}
