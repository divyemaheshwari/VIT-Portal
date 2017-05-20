<?php
     session_start();
     $regno=$_SESSION['regno'];
     include('../header1.php');
?>

 <li class="dropdown">
         <a class="dropdown-toggle" data-toggle="dropdown" href="#">Schedule
         <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="">Code Test</a></li>
            <li><a href="">MCQ Test</a></li>
          </ul>
        </li>
              <li><a href="candidate_management/candidate_management.php">Candidate Management</a></li>
              <li><a href="scoreboard.php">Reports</a></li>
              <li><a href="account.php">Profile</a></li>
        <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Code Test
         <span class="caret"></span></a>
            <ul class="dropdown-menu">
          <li><a href="admin-login/problems.php">Create Problem</a></li>
          <li><a href="scheduling_a_code_test/scheduling_a_code_test.php">Create Test</a></li>
          </ul>
        </li>
        <li><a href="">MCQ Test</a></li>
        <li><a href="database_page/searchdetails.php">Database</a></li>
        <li><a href="email_group/emailgroup.php">Create Email Group</a></li>
        <li><a href="admin-login/logout.php">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<html lang="en"><head>
    <div class="container">
       <center>
          <?php
       //    echo "hi";
                   if(isset($_POST['testid']))
                   {
                        
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "vit_programming_portal_2";

                        $testid=$_POST['testid'];
                      //  echo "Testid".$testid."<br>";
                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        // Check connection
                        if ($conn->connect_error) {
                              die("Connection failed: " . $conn->connect_error);
                           }

                        echo "<br><h3> Submissions under Test ID :".$_POST["testid"]."</h3><br>";
 
                        $associate=array();
                        $submissions=array();


                        $sql="SELECT students_classes.class_id AS classid, COUNT( * ) AS totalcount
                              FROM students_classes, student_tests, classes_tests, classes
                              WHERE student_tests.test_id ='$testid'
                              AND classes.faculty_id =  '$regno'
                              AND classes_tests.class_id = students_classes.class_id
                              AND students_classes.regno = student_tests.regno
                              AND classes_tests.test_id = student_tests.test_id
                              AND classes.class_id = classes_tests.class_id
                              GROUP BY students_classes.class_id";
 
                        if(!$result = $conn->query($sql)){
                                die('There was an error running the query [' . $conn->error . ']');
                          }


                       
                        
                        $count=1;

                         while($row=$result->fetch_assoc()){
                              $associate[$row['classid']]=0;
                              $submissions[$row['classid']]=$row['totalcount'];
                         }


                         $sql="SELECT students_classes.class_id as classid, count(*) as evaldone 
                               FROM students_classes, student_tests, classes_tests,classes
                               WHERE student_tests.test_id ='$testid' and classes.faculty_id='$regno'
                               AND student_tests.evaluation_status =1
                               AND classes_tests.class_id = students_classes.class_id
                               AND students_classes.regno = student_tests.regno
                               AND classes_tests.test_id = student_tests.test_id
                               AND classes.class_id=classes_tests.class_id
                               GROUP BY students_classes.class_id ";

                          if(!$result = $conn->query($sql)){
                                die('There was an error running the query [' . $conn->error . ']');
                          }
                        else
                               // echo "it worked <br/>";
                            
                          while($row=$result->fetch_assoc()){
                              $associate[$row['classid']]=$row['evaldone'];
                              //echo "hi".$associate[$row['classid']];
                              
                         }


                        echo "<br><table border='1' class='table table-striped' cellpadding='5' cellspacing='6' id='listofstudents'>";
                        echo "<tr><th>#</th><th>Class ID</th><th># of submissions</th>";
                        echo "<th>Evaluation Done</th><th>Not Done</th></tr>";

                         foreach($submissions as $key => $value){
                            echo "<tr><td><b>".$count++."</b></td>";
                            echo "<td>".$key."</td>";
                            echo "<td>".$submissions[$key]."</td>";
                            echo "<td>".$associate[$key]."</td>";
                            echo "<td>".($submissions[$key]-$associate[$key])."</td></tr>";
                            
                         }

                        echo "</table>";


                       $count=1;

                       //STUDENTS DETAILS UNDER TEST ID
                        Echo "<br><h3> Students Registered under Test ID :".$_POST["testid"]."</h3><br>";
                        

                        $sql = "SELECT students.regno as regno,students.name as name,
                                classes_tests.class_id as classid,student_tests.evaluation_status as evalstatus,
                                student_tests.marks as marks
                                FROM tests, student_tests, classes_tests, students, students_classes,classes
                                WHERE tests.test_id ='$testid' and classes.faculty_id='$regno'
                                AND tests.test_id = student_tests.test_id
                                AND classes_tests.class_id = students_classes.class_id
                                AND students_classes.regno = students.regno
                                AND students_classes.regno = student_tests.regno
                                AND classes_tests.test_id = tests.test_id
                                AND classes.class_id=classes_tests.class_id
                                ORDER BY student_tests.marks DESC , classes_tests.class_id
                              ";


                        //echo $sql;
                          //echo print_r($sql); 

                        if(!$result = $conn->query($sql)){
                                die('There was an error running the query [' . $conn->error . ']');
                          }
                        else
                                {}
                        $count=1;
                      
                        if(mysqli_num_rows($result)==0)
                          echo "<b>No students registered under the tests</b>";
                        else
                        {
                         echo "<br><table border='1' class='table table-striped' cellpadding='5' cellspacing='6' id='listofstudents'>";
                         echo "<tr><th>Rank</th><th>RegNo</th><th>Name</th><th>Classid</th><th>EvalStatus</th><th>Marks</th></tr>";
                         while($row=$result->fetch_assoc())
                             {
                               echo "<tr><td><b>".$count++."</b></td>";
                               echo "<td>".$row['regno']."</td>";
                               echo "<td>".$row['name']."</td>";
                               echo "<td>".$row['classid']."</td>";
                             
                               if($row['evalstatus']==1)
                                   echo "<td><form action = 'teacher_login/evaluate.php' method = 'post'>
                                        <button   class='btn btn-info'>ReEvaluate</button>
                                        <input type = 'hidden'  name = 'testid' value = '".$testid."'>
                                        <input type = 'hidden'  name = 'studentRegNo' value = '".$row['regno']."'>
                                        </form></td>";
                               else
                                   echo "<td><form action = 'teacher_login/evaluate.php' method = 'post'>
                                        <button id='".$row['regno'].'t'.$testid."' class='btn btn-primary'>Evaluate</button>
                                        <input type = 'hidden'  name = 'testid' value = '".$testid."'>
                                        <input type = 'hidden'  name = 'studentRegNo' value = '".$row['regno']."'>
                                        </form></td>";
                               
                              //echo "<td>".$row['evalstatus']."</td>";
                               echo "<td>".$row['marks']."</td></tr>";

                          }  
                        echo "</table>";
                       }
                         
       
                         $conn->close();



                   }


          ?>
           

       </center>
    </div> <!-- /container -->

    <hr/>
  </center>
   </html>
