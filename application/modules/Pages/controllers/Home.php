<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	function __construct()
    {
        parent::__construct();
        //$this->not_logged_in();
    }

    
	public function index()
	{   
		$data['courses'] = $this->Model->getCoursesWithDetails();
		$events = $this->Model->read('events', null, 'date_debut', 'ASC');
		$events_by_month = [];
           foreach ($events as $ev) {
             $key = $ev['mois'] . ' ' . $ev['annee'];

           if (!isset($events_by_month[$key])) {
            $events_by_month[$key] = [];
            }

             $events_by_month[$key][] = $ev;
            }
         $data['events_by_month'] = $events_by_month;

		$data['categories'] = $this->Model->read('categories', null, 'id_categorie');
		$data['events'] = $this->Model->read('events',['IsActive'=>1], 'id');
		$data['joinus'] = $this->Model->read('join_us', null, 'id');
		$data['parteners']=$this->Model->read('partener',['status'=>1],'id_partner');
		$data['carousels']=$this->Model->read('carousels',['IsActive'=>1],'IdCarousel');
		$data['about_us']=$this->Model->read('about_us',null,'id_about_us');
		$data['testimony']=$this->Model->read('testimonies',null,'IdTestimony');
		$this->load->view('Home_View',$data);
	}


	public function CreateNewsletter(){
		$email = $this->input->post('email');

        $data = array(
            'email' => $email
        );

        $rsp = $this->Model->create('newsletter', $data);

        $sms = [];
        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-success fade show mt-1 message" role="alert">
                             Email ajouté avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                             <strong>Oups!</strong> Cet email existe déjà ou une erreur est survenue.
                         </div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Pages/Home'));
	}




public function viewcourses($uuid)
{
    if (!$uuid) {
        show_404();
        return;
    }

    $sql = "SELECT c.* FROM courses c JOIN categories cat ON c.id_categorie = cat.id_categorie WHERE cat.uuid = ?";
    $query = $this->db->query($sql, [$uuid]);
    $data['cours'] = $query->result_array();

    // Récupérer la catégorie (optionnel pour afficher son nom)
    $sql_cat = "SELECT * FROM categories WHERE uuid = ?";
    $query_cat = $this->db->query($sql_cat, [$uuid]);
    $data['categorie'] = $query_cat->row_array();

    // Charger la vue
    $this->load->view('ViewCourses', $data);
}




  public function coursedetail($uuid)
{
    if (empty($uuid)) {
        show_404();
        return;
    }
    $sql = "
        SELECT co.*,   
               te.nom AS nom_teacher,   
               te.prenom AS prenom_teacher,  
               ca.nom_categories AS nom_categorie
        FROM courses co
        LEFT JOIN teachers te ON te.id_teacher = co.id_teacher
        LEFT JOIN categories ca ON ca.id_categorie = co.id_categorie
        WHERE co.uuid = ?
    ";
    $result = $this->Model->readQuery($sql, [$uuid]);

    if (empty($result)) {
        show_404();
        return;
    }
    $data['detailcourse'] = $result[0];

    


    $sql = "
        SELECT tc.*, c.nom_course, t.date_debut, t.date_defin
        FROM timetable_courses tc
        LEFT JOIN courses c ON tc.id_course = c.id_course
        LEFT JOIN timetable t ON tc.id_timetable = t.id_timetable
        WHERE c.uuid = ?
        ORDER BY tc.id_timetable_course DESC
    ";
    $data['timetable_courses'] = $this->Model->readQuery($sql,[$uuid]);

    $this->load->view('coursedetail', $data);
}


public function register($uuid){
     $sql = "
        SELECT tc.*, c.nom_course, c.uuid AS course_uuid, t.date_debut, t.date_defin
        FROM timetable_courses tc
        LEFT JOIN courses c ON tc.id_course = c.id_course
        LEFT JOIN timetable t ON tc.id_timetable = t.id_timetable
        WHERE tc.uuid = ?
        ORDER BY tc.id_timetable_course DESC
    ";
    $result= $this->Model->readQuery($sql,[$uuid]);

     $data['timetable_course'] = !empty($result) ? $result[0] : [];
    
    $data['mode_payements'] = $this->Model->read('mode_payement', null, 'id_mode_payement');
    $data['attendance_modes'] = $this->Model->read('attendace_course_mode', null, 'id_attendance');
    $this->load->view('Inscriptions_View',$data);
}

public function Categorie(){
    $data['categories'] = $this->Model->read('categories', null, 'id_categorie');
     $this->load->view('Categories_View',$data);

}

public function search(){
    $q = $this->input->get('q', true);
    $data['query'] = $q;
    $data['results'] = [];

    if (!empty($q)) {
        $this->db->like('nom_course', $q);
        $this->db->or_like('description', $q);
        $data['results']['courses'] = $this->db->get('courses')->result_array();

        $this->db->like('title', $q);
        $this->db->or_like('details', $q);
        $data['results']['posts'] = $this->db->get('news_media')->result_array();

        $this->db->like('Description', $q);
        $data['results']['galleries'] = $this->db->get('gallery')->result_array();
    }

    $this->load->view('Search_View', $data);
}
}