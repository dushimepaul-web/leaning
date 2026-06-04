<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *@author:    niyodon paci
 * Email:    niyodonpaci@gmail.com
 * Gitgub:    https://github.com/niyodon3564
*/
class Model extends CI_Model{


    function create($table, $data) {
        $this->load->helper('uuid');
        $data['uuid'] = generate_uuid();
        $query = $this->db->insert($table, $data);
        return ($query) ? true : false;
    }

    function read($table, $criteres = array(), $order_by_column = null, $order = 'DESC') {
    
    if (!empty($criteres)) {
        $this->db->where($criteres);
    }

    if (!empty($order_by_column)) {
        $this->db->order_by($order_by_column, $order);
    }

    $query = $this->db->get($table);
    return $query->result_array();
}

    function readWhereIdIn($table, $ids = array()) {
        $this->db->where('isDeleted !=', 1);
        $this->db->where('isApproved !=', 0);
        $this->db->where_in('idAccount', $ids);
        $query = $this->db->get($table);
        return $query->result_array();
    }



    function update($table, $criteres, $data) {
        $this->db->where($criteres);
        $query = $this->db->update($table, $data);
        return ($query) ? true : false;
    }


    function updateWhereIdIn($table, $ids = array()) {
    // $this->db->where('isDeleted !=', 1);
    date_default_timezone_set('Africa/Bujumbura');
    $date=date('Y-m-d:H:i:s');
    $this->db->where_in('idAccount', $ids);
    $query = $this->db->get($table);
    $result = $query->result_array();

    // Update isApproved to 0 for matching IDs
    $matchedIds = array_column($result, 'idAccount');
    $updateIds = array_intersect($ids, $matchedIds);

    if (!empty($updateIds)) {
        $this->db->where_in('idAccount', $updateIds);
        $this->db->update($table, array('isTreated' => 1,'dateTreated'=>$date));
    }

    return $result;
}



    function updateReturnAffectedRow($table, $criteres, $data) {
        $this->db->where($criteres);
        $this->db->update($table, $data);
        $affected_rows = $this->db->affected_rows();

        if ($affected_rows > 0) {
            $query = $this->db->get_where($table, $criteres);
            return $query->row_array();
        } else {
            return null;
        }
    }

    // loging with the specific permission
    public function getUserGroupByUserId($idUser){
      $sql = "SELECT * FROM user_group 
      INNER JOIN `groups` ON `groups`.idGroup = user_group.idGroup 
      WHERE user_group.idUser = ?";
      $query = $this->db->query($sql, array($idUser));
      $result = $query->row_array();

      return $result;
     }

    public function login($username, $password) {
        if($username && $password) {
          $sql = "SELECT * FROM user_group WHERE username = ?";
          $query = $this->db->query($sql, array($username));

          if($query->num_rows() == 1) {
            $result = $query->row_array();

            if (md5($password) === $result['password']) {
              return $result; 
            }
            else {
              return false;
            }

            
          }
          else {
            return false;
          }
        }
    }

    public function check_email($email){ // it checks if a specific username exist 
      if($email) {
        $sql = 'SELECT * FROM user_group WHERE username = ?';
        $query = $this->db->query($sql, array($email));
        $result = $query->num_rows();
        return ($result == 1) ? true : false;
      }

      return false;
    }
    
    function delete($table,$criteres){
        $this->db->where($criteres);
        $query = $this->db->delete($table);
        return ($query) ? true : false;
    }

    function readOne($table, $criteres) {
        $this->db->where($criteres);
        $query = $this->db->get($table);
        return $query->row_array();
    }

    function readQuery($query,$bindings = null){
      if (!is_null($bindings) && !empty($bindings)) {
            $query=$this->db->query($query, $bindings);
        } else {
            $query=$this->db->query($query);
        }

      if ($query) {
         return $query->result_array();
      }
    }

    function readQueryOne($query,$bindings = null){
      
      if (!is_null($bindings) && !empty($bindings)) {
            $query=$this->db->query($query, $bindings);
        } else {
            $query=$this->db->query($query);
        }
      if ($query) {
        return $query->row_array();
      }
    }


    function createLastId($table, $data) {
        $this->load->helper('uuid');
        $data['uuid'] = generate_uuid();
        $query = $this->db->insert($table, $data);
       if ($query) {
            return $this->db->insert_id();
        }
    }


    public function Set_History($idUser,$action,$description){
        $this->db->insert('logHistory', array(
                    'idUser' => $idUser,
                    'action' => $action,
                    'actionDescription' =>$description ));
    }

    
    
    function createBatch($table,$data){   
      $query=$this->db->insert_batch($table, $data);
      return ($query) ? true : false;
    }

    function readLimit($table,$limit)
    {
     $this->db->limit($limit);
     $query= $this->db->get($table);
     
      if($query)
       {
           return $query->result_array();
       }   
    }
 

    function updateBatch($table, $data, $where_key = 'uuid') {
        $query = $this->db->update_batch($table, $data, $where_key);
        return ($query) ? true : false;
    }


    function checkValue($table, $criteres) {
        $this->db->where($criteres);
        $query = $this->db->get($table);
        if($query->num_rows() > 0)
        {
           return true ;
        }
        else{
           return false;
       }
    }
    

    public function get_setting($key, $default = null)
     {
      $this->db->select('Value');
      $this->db->from('settings');
      $this->db->where('KeyValue', $key);
      $query = $this->db->get();

      if ($query->num_rows() > 0) {
        return $query->row()->Value;
     }

    return $default; 
    }

     public function setValueStore($key, $value)
    {
        $this->load->helper('uuid');
        $this->db->where('thekey', $key);
        $query = $this->db->get('settings');
        if ($query->num_rows() > 0) {
            $this->db->where('thekey', $key);
            if (!$this->db->update('settings', array('value' => $value))) {
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
        } else {
            $data = array('value' => $value, 'thekey' => $key, 'uuid' => generate_uuid());
            if (!$this->db->insert('settings', $data)) {
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
        }

    }
    

   // public function get_setting($key) {
    //    $this->db->where('KeyValue', $key);
   //     $query = $this->db->get('settings');

    //    if ($query->num_rows() > 0) {
     //       return $query->row()->Value;
     //   }

    //    return null;
    ////}





    

public function get_courses_by_categori($category_id) {
    return $this->db->get_where('courses', ['id_categorie' => $category_id])->result_array();
}
// Dans votre Model
public function getCoursesWithDetails()
{
    $this->db->select('c.*, cat.nom_categories, t.nom as nom_teacher, t.prenom as prenom_teacher');
    $this->db->from('courses c');
    $this->db->join('categories cat', 'c.id_categorie = cat.id_categorie', 'left');
    $this->db->join('teachers t', 'c.id_teacher = t.id_teacher', 'left');
    $this->db->order_by('c.id_course', 'DESC');
    return $this->db->get()->result_array();
}

// Dans votre Model
public function getTimetableCoursesWithDetails()
{
    $this->db->select('tc.*, c.nom_course','t.date_debut','t.date_fin');
    $this->db->from('timetable_courses tc');
    $this->db->join('courses c', 'tc.id_course = c.id_course', 'left');
    $this->db->join('timetable t', 'tc.id_timetable = t.id_timetable', 'left');
    $this->db->order_by('tc.id_timetable_course', 'DESC');
    return $this->db->get()->result_array();
}


// Dans votre Model
public function getInscriptionsWithDetails()
{
    $this->db->select('i.*, c.nom_course, tc.localisation, tc.price, 
                      am.nom_attendance, mp.description,
                      s.fullname as nom_student');
    $this->db->from('inscriptions i');
    $this->db->join('courses c', 'i.id_course = c.id_course', 'left');
    $this->db->join('timetable_courses tc', 'i.id_timetable_course = tc.id_timetable_course', 'left');
    $this->db->join('attendace_course_mode am', 'i.id_attendance = am.id_attendance', 'left');
    $this->db->join('mode_payement mp', 'i.id_mode_payement = mp.id_mode_payement', 'left');
    $this->db->join('students s', 'i.id_student = s.id_student', 'left');
    $this->db->order_by('i.id_inscription', 'DESC');
    return $this->db->get()->result_array();
}

public function getInscriptionDetails($id_inscription)
{
    $this->db->select('i.*, c.nom_course, tc.localisation, tc.price, 
                      am.nom_attendance, mp.nom_mode_payement,
                      s.nom as nom_student, s.prenom as prenom_student, s.email as email_student');
    $this->db->from('inscriptions i');
    $this->db->join('courses c', 'i.id_course = c.id_course', 'left');
    $this->db->join('timetable_courses tc', 'i.id_timetable_course = tc.id_timetable_course', 'left');
    $this->db->join('attendace_course_mode am', 'i.id_attendance = am.id_attendance', 'left');
    $this->db->join('mode_payement mp', 'i.id_mode_payement = mp.id_mode_payement', 'left');
    $this->db->join('students s', 'i.id_student = s.id_student', 'left');
    $this->db->where('i.id_inscription', $id_inscription);
    return $this->db->get()->row_array();
}

public function countWhere($table, $where)
{
    $this->db->where($where);
    return $this->db->count_all_results($table);
}







// Compter toutes les entrées d'une table
    public function count($table)
    {
        return $this->db->count_all($table);
    }


    // ⭐ Fonction pour obtenir les statistiques
    public function GetStatistics()
    {
        $data['total_inscriptions']   = $this->count('inscriptions');
        $data['paid_inscriptions']    = $this->countWhere('inscriptions', ['status_payement' => 'Payé']);
        $data['pending_inscriptions'] = $this->countWhere('inscriptions', ['status_payement' => 'En attente']);
        $data['completed_courses']    = $this->countWhere('inscriptions', ['status_ended_course' => 1]);

        return $data;
    }


    // Statistiques paiements
    public function count_paid_inscriptions() {
        return $this->db->where('status_payement', 'paid')->count_all_results('inscriptions');
    }

    public function count_pending_inscriptions() {
        return $this->db->where('status_payement', 'pending')->count_all_results('inscriptions');
    }

    public function count_failed_inscriptions() {
        return $this->db->where('status_payement', 'failed')->count_all_results('inscriptions');
    }

    // Validation email
    public function count_email_confirmed() {
        return $this->db->where('email_confirmed', 1)->count_all_results('inscriptions');
    }


    // Cours par catégorie
    public function get_courses_by_category() {
        $this->db->select('c.nom_categories, COUNT(crs.id_course) as course_count')
                 ->from('courses crs')
                 ->join('categories c', 'c.id_categorie = crs.id_categorie', 'left')
                 ->group_by('crs.id_categorie')
                 ->order_by('course_count', 'DESC');
        return $this->db->get()->result();
    }

    // Inscriptions par mois
    public function get_inscriptions_by_month() {
        $current_year = date('Y');
        $this->db->select("DATE_FORMAT(date_insertion, '%M') as month, COUNT(*) as count")
                 ->from('inscriptions')
                 ->where("YEAR(date_insertion) = $current_year")
                 ->group_by("MONTH(date_insertion), DATE_FORMAT(date_insertion, '%M')")
                 ->order_by("MONTH(date_insertion)");
        return $this->db->get()->result();
    }

    // Dernières inscriptions
    public function get_recent_inscriptions($limit = 10) {
        $this->db->select('i.*, s.fullname, s.email, c.nom_course, mp.description as payment_mode')
                 ->from('inscriptions i')
                 ->join('students s', 's.id_student = i.id_student')
                 ->join('courses c', 'c.id_course = i.id_course')
                 ->join('mode_payement mp', 'mp.id_mode_payement = i.id_mode_payement', 'left')
                 ->order_by('i.date_insertion', 'DESC')
                 ->limit($limit);
        return $this->db->get()->result();
    }

    // Derniers étudiants
    public function get_recent_students($limit = 5) {
        $this->db->select('*')
                 ->from('students')
                 ->order_by('id_student', 'DESC')
                 ->limit($limit);
        return $this->db->get()->result();
    }

    // Cours les plus populaires
    public function get_top_courses($limit = 5) {
        $this->db->select('c.nom_course, COUNT(i.id_inscription) as inscription_count')
                 ->from('courses c')
                 ->join('inscriptions i', 'i.id_course = c.id_course', 'left')
                 ->group_by('c.id_course')
                 ->order_by('inscription_count', 'DESC')
                 ->limit($limit);
        return $this->db->get()->result();
    }

    // Statistiques modes de paiement
    public function get_payment_methods_stats() {
        $this->db->select('mp.description, COUNT(i.id_inscription) as count')
                 ->from('inscriptions i')
                 ->join('mode_payement mp', 'mp.id_mode_payement = i.id_mode_payement', 'left')
                 ->group_by('i.id_mode_payement')
                 ->order_by('count', 'DESC');
        return $this->db->get()->result();
    }

    // Statistiques modes de présence
    public function get_attendance_mode_stats() {
        $this->db->select('ac.nom_attendance, COUNT(i.id_inscription) as count')
                 ->from('inscriptions i')
                 ->join('attendace_course_mode ac', 'ac.id_attendance = i.id_attendance', 'left')
                 ->group_by('i.id_attendance')
                 ->order_by('count', 'DESC');
        return $this->db->get()->result();
    }

    // Formateurs avec plus d'étudiants
    public function get_top_teachers($limit = 5) {
        $this->db->select("CONCAT(t.nom, ' ', t.prenom) as teacher_name, 
                          COUNT(DISTINCT i.id_student) as student_count, 
                          COUNT(DISTINCT c.id_course) as course_count")
                 ->from('teachers t')
                 ->join('courses c', 'c.id_teacher = t.id_teacher', 'left')
                 ->join('inscriptions i', 'i.id_course = c.id_course', 'left')
                 ->group_by('t.id_teacher')
                 ->order_by('student_count', 'DESC')
                 ->limit($limit);
        return $this->db->get()->result();
    }

    // Évolution des inscriptions (derniers mois)
    public function get_inscription_trend($months = 6) {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $start_date = date('Y-m-01', strtotime($month));
            $end_date = date('Y-m-t', strtotime($month));
            
            $count = $this->db->where('date_insertion >=', $start_date)
                             ->where('date_insertion <=', $end_date)
                             ->count_all_results('inscriptions');
            
            $result[] = [
                'month' => date('M Y', strtotime($month)),
                'count' => $count
            ];
        }
        return $result;
    }

    // Données pour les graphiques
    public function get_inscriptions_by_month_chart() {
        $data = $this->get_inscriptions_by_month();
        $labels = [];
        $values = [];
        
        foreach ($data as $item) {
            $labels[] = $item->month;
            $values[] = (int)$item->count;
        }
        
        return ['labels' => $labels, 'values' => $values];
    }

    public function get_attendance_mode_chart() {
        $data = $this->get_attendance_mode_stats();
        $labels = [];
        $values = [];
        
        foreach ($data as $item) {
            $labels[] = $item->nom_attendance;
            $values[] = (int)$item->count;
        }
        
        return ['labels' => $labels, 'values' => $values];
    }

    // Récupérer les données pour la carte géographique
    public function get_students_by_country() {
        $this->db->select('your_country, COUNT(*) as student_count')
                 ->from('inscriptions')
                 ->group_by('your_country')
                 ->order_by('student_count', 'DESC');
        return $this->db->get()->result();
    }

  }  
 

