<?php
class ControllerCommonLanguage extends Controller {
	public function index() {
		$this->load->language('common/language');

		$data['code'] = $this->config->get('config_language');

		$url_data = $this->request->get;

		if (isset($url_data['route'])) {
			$route = $url_data['route'];
		} else {
			$route = $this->config->get('action_default');
		}

		unset($url_data['route']);
		unset($url_data['_route_']);
		unset($url_data['language']);

		$url = '';

		if ($url_data) {
			$url = '&' . urldecode(http_build_query($url_data));
		}

		$data['languages'] = array();

		$this->load->model('localisation/language');

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
            $data['languages'][] = array(
                'name' => $result['name'],
                'code' => $result['code'],
                'href' => $this->url->link('common/language/language', 'language=' . $this->config->get('config_language') . '&code=' . $result['code'] . '&redirect=' . urlencode(str_replace('&amp;', '&', $this->url->link($route, 'language=' . $result['code'] . $url))))
            );
		}

		return $this->load->view('common/language', $data);
	}

	public function language() {
		if (isset($this->request->get['code'])) {
			$code = $this->request->get['code'];
		} else {
			$code = $this->config->get('config_language');
		}

		if (isset($this->request->get['redirect'])) {
			$redirect =  htmlspecialchars_decode($this->request->get['redirect'], ENT_COMPAT, 'UTF-8');
		} else {
			$redirect = '';
		}

		$option = array(
			'expires'  => time() + 60 * 60 * 24 * 30,
			'path'     => '/',
			'SameSite' => 'lax'
		);

		setcookie('language', $code, $option);

		if ($redirect && substr($redirect, 0, strlen($this->config->get('config_url'))) == $this->config->get('config_url')) {
			$this->response->redirect($redirect);
		} else {
			$this->response->redirect($this->url->link($this->config->get('action_default'), 'language=' . $code));
		}
	}
}