import axios from 'axios';

export default function (endpoint, { method = 'GET', cancelToken, data } = {}) {
	return axios({
		method,
		url: endpoint,
		cancelToken,
		data
	});
}
