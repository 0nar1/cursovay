(function() {
	const routes = {};
	let notFoundHandler = null;

	function on(path, handler) { routes[path] = handler; }
	function otherwise(handler) { notFoundHandler = handler; }

	function parseLocation() {
		const hash = location.hash || '#/';
		const [path, query = ''] = hash.slice(1).split('?');
		const params = Object.fromEntries(new URLSearchParams(query));
		return { path: '/' + path.replace(/^\/+/, ''), params };
	}

	async function render() {
		const { path, params } = parseLocation();
		const handler = routes[path] || notFoundHandler;
		if (!handler) return;
		const app = document.getElementById('app');
		app.setAttribute('aria-busy', 'true');
		try {
			const html = await handler({ params });
			app.innerHTML = html;
			app.focus();
			refreshNav();
		} catch (e) {
			app.innerHTML = `<div class="card"><h3>Ошибка</h3><p class="muted">${e?.message || e}</p></div>`;
			console.error(e);
		} finally {
			app.removeAttribute('aria-busy');
		}
		document.querySelectorAll('[data-link]').forEach(a => {
			if (a.getAttribute('href') === '#' + path) a.setAttribute('aria-current', 'page');
			else a.removeAttribute('aria-current');
		});
	}

	function refreshNav() {
		const user = (typeof DataStore !== 'undefined' && DataStore.currentUser) ? DataStore.currentUser() : null;
		const nav = document.getElementById('nav-menu');
		if (!nav) return;
		let links = [
			`<li><a href="#/" data-link>Главная</a></li>`,
			`<li><a href="#/courses" data-link>Курсы</a></li>`,
			`<li><a href="#/schedule" data-link>Расписание</a></li>`
		];
		if (user?.role === 'Студент') {
			links.push(`<li><a href="#/homework" data-link>Моё ДЗ</a></li>`);
		}
		if (user?.role === 'Преподаватель' || user?.role === 'Администратор') {
			links.push(`<li><a href="#/students" data-link>Студенты</a></li>`);
		}
		if (user?.role === 'Администратор') {
			links.push(`<li><a href="#/admin" data-link>Управление</a></li>`);
		}
		links.push(`<li><a href="#/feedback" data-link>Обратная связь</a></li>`);
		if (user) {
			const label = `${user.name || user.email} (${user.role})`;
			links.push(`<li><a href="#/account" data-link title="Профиль">👤 ${label}</a></li>`);
			links.push(`<li><button class="btn ghost" title="Выйти из аккаунта" onclick="Pages.onLogout()">Выйти</button></li>`);
		} else {
			links.push(`<li><a href="#/login" data-link>Войти</a></li>`);
			links.push(`<li><a href="#/register" data-link>Регистрация</a></li>`);
		}
		nav.innerHTML = links.join('');
	}

	window.addEventListener('hashchange', render);
	window.addEventListener('DOMContentLoaded', () => {
		const year = document.getElementById('year');
		if (year) year.textContent = String(new Date().getFullYear());
		document.querySelector('.nav-toggle')?.addEventListener('click', () => {
			document.getElementById('nav-menu')?.classList.toggle('open');
		});
		render();
	});

	window.Router = { on, otherwise, render };
})();


