<?php //core-student.php
class Student
{
    public $name = "";
    public $account = "";
    public $role = 0;
    public $userid = 0;
    
    public function __construct($in_name, $in_account, $in_role, $in_userid)
    {
        $this->name = $in_name;
        $this->account = $in_account;
        $this->role = $in_role;
        $this->userid = $in_userid;
    }
}

function student_cmp ($student1, $student2)
{
    if($student1->role != $student2->role)
    {
        return ($student1->role > $student2->role) ? -1 : 1;
    }
    return strcmp($student1->name, $student2->name);
}
?>