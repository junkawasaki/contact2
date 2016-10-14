<?php
class Model_Contacts extends Model
{
    public static $title_jp = array(0=>'', 1=>'ご意見', 2=>'ご感想', 3=>'その他');

    public function insert($data = null)
    {
        $sql = 'INSERT INTO contacts (title, name, address, tel, content) VALUES (:title, :name, :address, :tel, :content)';
        $query = DB::query($sql);
        
        $params = array('title'=>$data->title, 'name'=>$data->name, 'address'=>$data->address, 'tel'=>$data->tel, 'content'=>$data->content);
        $query->parameters($params);
        
        $query->execute();
    }

    public function count()
    {
        $sql = 'SELECT COUNT(id) FROM contacts WHERE del_flag=0';
        $query = DB::query($sql);
        $result = $query->execute()->as_array();
        return $result[0];
    }

    public function get_list($pagination)
    {
        $sql = 'SELECT id, title, name, address, timestamp FROM contacts WHERE del_flag=0 LIMIT :limit OFFSET :offset';
        $query = DB::query($sql);

        $params = array('limit'=>$pagination->per_page, 'offset'=>$pagination->offset);
        $query->parameters($params);
        $result = $query->execute()->as_array();

        foreach ($result as $i=>&$contact) {
            $contact['title_jp'] = self::$title_jp[$contact['title']];
        }

        return $result;
    }

    public function get_detail($id)
    {
        $sql = 'SELECT id, title, name, address, tel, content, timestamp FROM contacts WHERE id=:id AND del_flag=0';
        $query = DB::query($sql);

        $params = array('id'=>$id);
        $query->parameters($params);
        $result = $query->execute()->as_array();

        foreach ($result as $i=>&$contact) {
            $contact['title_jp'] = self::$title_jp[$contact['title']];
        }

        return $result;
    }
}
