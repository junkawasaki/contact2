<?php
class Model_Administrators extends Model
{
    public function check_signin($input = array())
    {
        $sql = 'SELECT COUNT(id) FROM administrators WHERE name=:name AND password=:password AND del_flag=0';
        $query = DB::query($sql);

        $params = array('name'=>$input['id'], 'password'=>$input['password']);
        $query->parameters($params);

        $result = $query->execute()->as_array();
        return !empty($result[0]['COUNT(id)']) ? $result[0]['COUNT(id)'] : false;
    }
}
