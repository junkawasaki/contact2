<?php
class Controller_Admin extends Controller
{
	public function action_index()
	{
        $data = new stdClass();
        $data->csrf_token_key = Config::get('security.csrf_token_key');
        $data->csrf_token_value = Security::fetch_token();

        return Response::forge(View::forge('admin/index.twig', $data));
	}

	public function action_signin()
	{
        if (!Security::check_token()) {
        	// csrfエラー
            $data = new stdClass();
            $data->csrf_token_key = Config::get('security.csrf_token_key');
            $data->csrf_token_value = Security::fetch_token();
            $data->error = 'csrfエラー';
            return Response::forge(View::forge('admin/index.twig', $data));
        }

        $val = Validation::forge();
        $val->add_field('id', 'ID', 'required');
        $val->add_field('password', 'Password', 'required');

        $input = array();
        $input['id'] = Input::post('id');
        $input['password'] = Input::post('password');

        $administrators = new Model_Administrators();
        if ($val->run() && $administrators->check_signin($input)) {
            Session::set('admin', $input);
        	Response::redirect('/admin/list');
        } else {
            // ログインエラー
            $data = new stdClass();
            $data->id = $input['id'];
            $data->csrf_token_key = Config::get('security.csrf_token_key');
            $data->csrf_token_value = Security::fetch_token();
            $data->error = 'ログインエラー';
            return Response::forge(View::forge('admin/index.twig', $data));
        }
	}

    public function action_list()
    {
        if (!Session::get('admin')) {
        	// セッションエラー
            $data = new stdClass();
            $data->csrf_token_key = Config::get('security.csrf_token_key');
            $data->csrf_token_value = Security::fetch_token();
            $data->error = 'セッションエラー';
            return Response::forge(View::forge('admin/index.twig', $data));
        }

        $m_contacts = new Model_Contacts();
        $count = $m_contacts->count();
        $config = array(
            'pagination_url' => 'admin/list',
            'uri_segment' => 3,
            'num_links' => 2,
            'per_page' => 10,
            'total_items' => $count['COUNT(id)'],
            'show_first' => true,
            'show_last' => true,
            'name' => 'bootstrap3',
        );

        $data = new stdClass();
        $data->pagination = Pagination::forge('bootstrap3', $config);
        $data->list = $m_contacts->get_list($data->pagination);

        return Response::forge(View::forge('admin/list.twig', $data, false));
    }

    public function action_detail($id)
    {
        if (!Session::get('admin')) {
        	// セッションエラー
            $data = new stdClass();
            $data->csrf_token_key = Config::get('security.csrf_token_key');
            $data->csrf_token_value = Security::fetch_token();
            $data->error = 'セッションエラー';
            return Response::forge(View::forge('admin/index.twig', $data));
        }

        $data = new stdClass();
        $m_contacts = new Model_Contacts();
        $data->detail = $m_contacts->get_detail($id);
        return Response::forge(View::forge('admin/detail.twig', $data));
    }
}

