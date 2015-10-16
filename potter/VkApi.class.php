<?
//Simple class for VK Api by Potter

	class VkApi
	{
		public $app_id;
		public $access_token;

		public function getAuthLink($scope)
		{
			echo "<a href='https://oauth.vk.com/authorize?client_id=".$this->app_id."&scope=".$scope.",offline&redirect_uri=https://oauth.vk.com/blank.html&response_type=token' target=_bank>Получить токен!</a>";
		}
		public function query($method, $arguments_list)
		{
			$arguments = "";
			if($arguments_list != null)
				foreach ($arguments_list as $key => $value) {
					$arguments .= $key."=".urlencode($value)."&";
				}
			$query = file_get_contents("https://api.vk.com/method/".$method."?".$arguments."access_token=".$this->access_token);
			return json_decode($query);
		}
	}