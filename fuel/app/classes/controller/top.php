<?php
class Controller_Top extends Controller
{
	public function action_index()
	{
        $contact = Session::get('contact');

        $data = new stdClass();
        $data->csrf_token_key = Config::get('security.csrf_token_key');
        $data->csrf_token_value = Security::fetch_token();
        $data->title = !empty($contact['title']) ? $contact['title'] : 0;
        $data->name = !empty($contact['name']) ? $contact['name'] : '';
        $data->address = !empty($contact['address']) ? $contact['address'] : '';
        $data->tel = !empty($contact['tel']) ? $contact['tel'] : '';
        $data->content = !empty($contact['content']) ? $contact['content'] : '';
        $data->error = !empty($contact['error']) ? $contact['error'] : '';

        return Response::forge(View::forge('top/index.twig', $data));
	}

	public function action_input()
	{
        if (!Security::check_token()) {
        	// csrfエラー
            Session::set('contact', array('error'=>'csrfエラー'));
        	Response::redirect('/');
        }

        $val = Validation::forge();
        $val->add_field('title', '件名', 'required');
        $val->add_field('name', 'お名前', 'required');
        $val->add_field('address', 'メールアドレス', 'required|valid_email');
        $val->add_field('tel', '電話番号', 'required');
        $val->add_field('content', 'お問い合わせ内容', 'required');

        $contact = array(
            'title'   => Input::post('title'),
            'name'    => Input::post('name'),
            'address' => Input::post('address'),
            'tel'     => Input::post('tel'),
            'content' => Input::post('content'),
        );

        if ($val->run()) {
            Session::set('contact', $contact);
        	Response::redirect('/top/confirm');
        } else {
            // 入力エラー
            $contact['error'] = '入力エラー';
            Session::set('contact', $contact);
        	Response::redirect('/');
        }
	}

	public function action_confirm()
	{
        $contact = Session::get('contact');

        $data = new stdClass();
        $data->title_jp = Model_Contacts::$title_jp[$contact['title']];
        $data->name = $contact['name'];
        $data->address = $contact['address'];
        $data->tel = $contact['tel'];
        $data->content = $contact['content'];
        $data->csrf_token_key = Config::get('security.csrf_token_key');
        $data->csrf_token_value = Security::fetch_token();

        return Response::forge(View::forge('top/confirm.twig', $data));
	}

	public function action_complete()
	{
        if (!Security::check_token()) {
        	// csrfエラー
            Session::set('contact', array('error'=>'csrfエラー'));
        	Response::redirect('/');
        }

        $contact = Session::get('contact');

        $data = new stdClass();
        $data->title = $contact['title'];
        $data->title_jp = Model_Contacts::$title_jp[$contact['title']];
        $data->name = $contact['name'];
        $data->address = $contact['address'];
        $data->tel = $contact['tel'];
        $data->content = $contact['content'];

        $m_contacts = new Model_Contacts();
        $m_contacts->insert($data);

		return Response::forge(View::forge('top/complete.twig'));
	}

	public function action_404()
	{
        // 404エラー
        Session::set('contact', array('error'=>'404エラー'));
    	Response::redirect('/', 'location', 404);
	}
}
