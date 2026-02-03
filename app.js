(function() {
	function pageHome() {
		return `
			<section class="hero">
				<div class="panel">
					<h1>Академия «TOP» — обучение цифровым навыкам</h1>
					<p>Программы для детей и взрослых: программирование, дизайн, аналитика. Учитесь в удобном формате, следите за расписанием и управляйте обучением онлайн.</p>
					<div style="display:flex; gap:.5rem; margin-top:.75rem">
						<a class="btn" href="#/courses">Выбрать программу</a>
						<a class="btn ghost" href="#/schedule">Ближайшие занятия</a>
					</div>
				</div>
				<div class="panel">
					<div class="grid cols-2">
						<div class="card service-card-1"><h3>Онлайн‑платформа</h3><p class="muted">Материалы и задания доступны 24/7.</p></div>
						<div class="card service-card-2"><h3>Поддержка преподавателей</h3><p class="muted">Ответы на вопросы и разбор задач.</p></div>
						<div class="card service-card-3"><h3>Современная программа</h3><p class="muted">Актуальные технологии и практики.</p></div>
						<div class="card service-card-4"><h3>Карьерное консультирование</h3><p class="muted">Помогаем со стажировками и проектами.</p></div>
					</div>
				</div>
			</section>

		<section class="section">
			<h2 class="section-title">Программы</h2>
			<div class="grid cols-4">
				<div class="card program-card-1"><h3>7–8 лет</h3><p class="muted">Знакомство с логикой и визуальным кодом.</p><a class="btn blue" href="#/courses">Подробнее</a></div>
				<div class="card program-card-2"><h3>9–12 лет</h3><p class="muted">Scratch, основы Python и веб‑страниц.</p><a class="btn green" href="#/courses">Подробнее</a></div>
				<div class="card program-card-3"><h3>13–14 лет</h3><p class="muted">JavaScript, алгоритмы, проектная работа.</p><a class="btn orange" href="#/courses">Подробнее</a></div>
				<div class="card program-card-4"><h3>15–55 лет</h3><p class="muted">Профессии: фронтенд, аналитик, QA, дизайн.</p><a class="btn purple" href="#/courses">Подробнее</a></div>
			</div>
		</section>

		<section class="section">
			<h2 class="section-title">Сервисы</h2>
			<div class="grid cols-2">
				<div class="card service-card-1"><h3>Студенту</h3><p class="muted">Личный кабинет, прогресс, домашние задания.</p></div>
				<div class="card service-card-2"><h3>Абитуриенту</h3><p class="muted">Подбор программы и консультации.</p></div>
				<div class="card service-card-3"><h3>Педагогу</h3><p class="muted">Инструменты для проведения занятий.</p></div>
				<div class="card service-card-4"><h3>Онлайн‑оплата</h3><p class="muted">Удобные и безопасные способы оплаты.</p></div>
			</div>
		</section>

		<section class="section">
			<h2 class="section-title">Новости</h2>
			<div class="grid cols-2">
				<div class="card news-card"><h3>Скидки ко дню рождения</h3><p class="muted">Именинникам — минус 15% на обучение в месяц праздника.</p><a class="btn ghost" href="#/feedback">Уточнить детали</a></div>
				<div class="card news-card"><h3>Акция 1+1</h3><p class="muted">Приведи друга и оба получите бонусы на обучение.</p><a class="btn ghost" href="#/feedback">Задать вопрос</a></div>
			</div>
		</section>

		<section class="section">
			<h2 class="section-title">Контакты</h2>
			<div class="card">
				<p><strong>Телефон:</strong> +7 (495) 743‑53‑05</p>
				<p><strong>Адрес:</strong> Сергиев Посад, пр. Красной Армии, 212А, корп. 1, офис 12</p>
				<p class="muted">Задайте вопрос через форму <a href="#/feedback">обратной связи</a> — ответим в ближайшее время.</p>
			</div>
		</section>
		`;
	}

	async function pageCourses() {
		const data = await DataStore.load();
		const list = data.courses || [];
		return `
			<section>
				<h2>Каталог курсов</h2>
				<div class="toolbar">
					<div class="filters">
						<input id="q" placeholder="Поиск по названию/тегам" oninput="Pages.onCourseSearch(this.value)">
						<select id="level" onchange="Pages.onCourseFilterLevel(this.value)">
							<option value="">Все уровни</option>
							<option>Начинающий</option>
							<option>Средний</option>
							<option>Продвинутый</option>
						</select>
					</div>
				</div>
				<div id="coursesList" class="grid cols-3">${list.map(UI.courseCard).join('')}</div>
			</section>
		`;
	}

	async function pageSchedule() {
		const data = await DataStore.load();
		const byId = Object.fromEntries((data.courses || []).map(c => [c.id, c]));
		const groupsById = Object.fromEntries((data.groups || []).map(g => [g.id, g]));
		const rows = (data.schedule || []).map(s => UI.scheduleRow(s, byId, groupsById)).join('');
		return `
			<section>
				<h2>Расписание</h2>
				<div class="card">
					<div class="table-wrapper">
						<table class="table">
							<thead><tr><th>День</th><th>Время</th><th>Курс</th><th>Группа</th><th>Аудитория</th></tr></thead>
							<tbody>${rows || `<tr><td colspan="5">${UI.emptyState('Нет занятий')}</td></tr>`}</tbody>
						</table>
					</div>
				</div>
			</section>
		`;
	}

	function pageAccount() {
		const p = DataStore.getProfile();
		const user = DataStore.currentUser();
		const role = user?.role || p.role;
		return `
			<section>
				<h2>Личный кабинет</h2>
				<form class="card" onsubmit="Pages.onProfileSave(event)">
					<div class="row">
						<div>
							<label for="name">Имя</label>
							<input id="name" name="name" value="${escapeHtml(p.name)}" required>
						</div>
						<div>
							<label for="email">Email</label>
							<input id="email" name="email" type="email" value="${escapeHtml(p.email)}" required>
						</div>
					</div>
					<div>
						<label>Роль</label>
						<div class="pill">${escapeHtml(role)}</div>
					</div>
					<div style="display:flex; gap:.5rem; margin-top:.75rem">
						<button class="btn" type="submit">Сохранить</button>
						<button class="btn ghost" type="button" onclick="Pages.onProfileReset()">Сбросить</button>
					</div>
				</form>
			</section>
		`;
	}

	function pageFeedback() {
		return `
			<section>
				<h2>Обратная связь</h2>
				<form class="card" onsubmit="Pages.onFeedbackSubmit(event)">
					<div class="row">
						<div>
							<label for="topic">Тема</label>
							<input id="topic" name="topic" placeholder="Вопрос по курсу..." required>
						</div>
						<div>
							<label for="course">Курс</label>
							<input id="course" name="course" placeholder="Например: JavaScript Базовый">
						</div>
					</div>
					<div>
						<label for="message">Сообщение</label>
						<textarea id="message" name="message" rows="5" required></textarea>
					</div>
					<div style="display:flex; gap:.5rem; margin-top:.75rem">
						<button class="btn" type="submit">Отправить</button>
					</div>
				</form>
				<div id="feedbackToast" class="sr-only" aria-live="polite"></div>
			</section>
		`;
	}

	function pageLogin() {
		return `
			<section>
				<h2>Вход</h2>
				<form class="card" onsubmit="Pages.onLogin(event)">
					<label for="loginEmail">Email</label>
					<input id="loginEmail" name="email" type="email" required>
					<label for="loginPassword">Пароль</label>
					<input id="loginPassword" name="password" type="password" required>
					<div style="display:flex; gap:.5rem; margin-top:.75rem">
						<button class="btn" type="submit">Войти</button>
						<a class="btn ghost" href="#/register">Регистрация</a>
					</div>
					<div class="login-demo">
						<p>Демо-доступ:</p>
						<ul>
							<li><strong>Админ:</strong> admin@top.local / admin</li>
							<li><strong>Преподаватель:</strong> teacher1@top.local / teacher1</li>
							<li><strong>Студент:</strong> student1@top.local / student1</li>
						</ul>
					</div>
				</form>
			</section>
		`;
	}

	function pageRegister() {
		return `
			<section>
				<h2>Регистрация</h2>
				<form class="card" onsubmit="Pages.onRegister(event)">
					<div class="row">
						<div>
							<label for="regName">Имя</label>
							<input id="regName" name="name" required>
						</div>
						<div>
							<label for="regEmail">Email</label>
							<input id="regEmail" name="email" type="email" required>
						</div>
					</div>
					<label for="regPassword">Пароль</label>
					<input id="regPassword" name="password" type="password" minlength="6" required>
					<p class="input-hint">Минимум 6 символов, учитываются буквы, цифры и знаки.</p>
					<div style="display:flex; gap:.5rem; margin-top:.75rem">
						<button class="btn" type="submit">Создать аккаунт</button>
						<a class="btn ghost" href="#/login">У меня уже есть аккаунт</a>
					</div>
				</form>
			</section>
		`;
	}

	function pageStudentHomework() {
		const user = DataStore.currentUser();
		if (!user) return redirectLogin();
		
		const studentGroups = DataStore.getGroupsForStudent(user.id);
		const groups = DataStore.getGroups();
		const groupById = Object.fromEntries(groups.map(g => [g.id, g]));
		
		const allHomework = DataStore.listHomeworkForStudent(user.id);
		const list = allHomework.filter(h => studentGroups.includes(h.groupId));
		
		const db = (JSON.parse(localStorage.getItem('top-academy-db'))||{});
		const grades = (db.grades||[]).filter(g => g.studentId === user.id);
		const scheduleById = Object.fromEntries((db.schedule||[]).map(s => [s.id, s]));
		
		return `
			<section>
				<h2>Мои задания</h2>
				<div class="card">
					<h3>Мои группы</h3>
					<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
						${studentGroups.map(gId => {
							const group = groupById[gId];
							return group ? `<span class="pill blue">${group.name}</span>` : '';
						}).join('')}
					</div>
					<div class="table-wrapper">
						<table class="table">
							<thead><tr><th>Курс</th><th>Группа</th><th>Задание</th><th>Описание</th><th>Оценка</th></tr></thead>
							<tbody>
								${(list||[]).map(h => {
									const group = groupById[h.groupId];
									return `<tr>
										<td>${h.courseId}</td>
										<td>${group ? group.name : h.groupId}</td>
										<td>${h.title}</td>
										<td>${h.description||''}</td>
										<td>${h.grade??'-'}</td>
									</tr>`;
								}).join('') || `<tr><td colspan="5">Пока нет заданий</td></tr>`}
							</tbody>
						</table>
					</div>
				</div>
				<div class="card" style="margin-top:1rem">
					<h3>Мои оценки по занятиям</h3>
					<div class="table-wrapper">
						<table class="table">
							<thead><tr><th>Дата/день</th><th>Время</th><th>Курс</th><th>Группа</th><th>Оценка</th></tr></thead>
							<tbody>
								${grades.map(g => { 
									const s = scheduleById[g.scheduleId]||{}; 
									const group = groupById[s.groupId];
									return `<tr>
										<td>${s.weekday||s.date||''}</td>
										<td>${s.time||''}</td>
										<td>${s.courseId||''}</td>
										<td>${group ? group.name : s.groupId}</td>
										<td>${g.grade}</td>
									</tr>`; 
								}).join('') || `<tr><td colspan="5">Оценок пока нет</td></tr>`}
							</tbody>
						</table>
					</div>
				</div>
			</section>
		`;
	}

function pageTeacherStudents() {
    const user = DataStore.currentUser();
    if (!user) return redirectLogin();
    if (user.role === 'Студент') return `<div class="card">Недостаточно прав</div>`;
    
    const teacherGroups = DataStore.getGroupsForTeacher(user.id);
    const groups = DataStore.getGroups();
    const groupById = Object.fromEntries(groups.map(g => [g.id, g]));
    
    const allUsers = DataStore.getUsers();
    const students = allUsers.filter(u => u.role === 'Студент' && 
        (u.groups || []).some(gId => teacherGroups.includes(gId)));
    
    const db = (JSON.parse(localStorage.getItem('top-academy-db'))||{});
    const schedule = (db.schedule || []).filter(s => teacherGroups.includes(s.groupId));
    const courses = db.courses || [];
    const courseById = Object.fromEntries(courses.map(c => [c.id, c]));
    
    return `
        <section>
            <h2>Мои группы и студенты</h2>
            <div class="card">
                <h3>Мои группы</h3>
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                    ${teacherGroups.map(gId => {
                        const group = groupById[gId];
                        return group ? `<span class="pill green">${group.name}</span>` : '';
                    }).join('')}
                </div>
                <p class="muted">Доступно занятий: ${schedule.length}</p>
            </div>
            
            <div class="card">
                <h3>Студенты в моих группах</h3>
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>Имя</th><th>Email</th><th>Группы</th><th>Действия</th></tr></thead>
                        <tbody>
                            ${students.map(s => {
                                const studentGroups = s.groups || [];
                                const myGroups = studentGroups.filter(gId => teacherGroups.includes(gId));
                                return `<tr>
                                    <td>${s.name}</td>
                                    <td>${s.email}</td>
                                    <td>${myGroups.map(gId => {
                                        const group = groupById[gId];
                                        return group ? `<span class="pill blue">${group.name}</span>` : '';
                                    }).join(' ')}</td>
                                    <td>
                                        <button class="btn ghost" onclick="Pages.openAssignHw('${s.id}')">Задать ДЗ</button>
                                    </td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div id="hwModal" class="card" style="display:none; margin-top:1rem">
                <h3>Новое задание</h3>
                <form class="form-stack" onsubmit="Pages.onAssignHw(event)">
                    <input type="hidden" name="studentId" id="hwStudentId">
                    <label>Курс</label>
                    <select name="courseId" required>
                        <option value="">Выберите курс</option>
                        ${courses.map(c => `<option value="${c.id}">${c.title}</option>`).join('')}
                    </select>
                    <label>Группа</label>
                    <select name="groupId" required>
                        <option value="">Выберите группу</option>
                        ${teacherGroups.map(gId => {
                            const group = groupById[gId];
                            return group ? `<option value="${gId}">${group.name}</option>` : '';
                        }).join('')}
                    </select>
                    <label>Название</label>
                    <input name="title" required>
                    <label>Описание</label>
                    <textarea name="description" rows="3"></textarea>
                    <div style="margin-top:.5rem">
                        <button class="btn" type="submit">Создать</button>
                        <button class="btn ghost" type="button" onclick="Pages.closeAssignHw()">Отмена</button>
                    </div>
                </form>
            </div>

            <div class="card" style="margin-top:1rem">
                <h3>Оценки по занятию</h3>
                <form class="form-stack" onsubmit="Pages.onSetGrades(event)">
                    <label>Занятие</label>
                    <select name="scheduleId" required>
                        <option value="">Выберите занятие</option>
                        ${schedule.map(s => {
                            const group = groupById[s.groupId];
                            const course = courseById[s.courseId];
                            return `<option value="${s.id}">${s.weekday||s.date||''} ${s.time||''} — ${course ? course.title : s.courseId} (${group ? group.name : s.groupId})</option>`;
                        }).join('')}
                    </select>
                    <div class="grid cols-2" style="margin-top:.5rem">
                        ${students.map(s => `<div><label>${s.name}</label><input type=\"number\" min=\"1\" max=\"12\" step=\"1\" name=\"grade_${s.id}\" placeholder=\"Оценка 1-12\"></div>`).join('')}
                    </div>
                    <div style="margin-top:.5rem"><button class="btn" type="submit">Сохранить оценки</button></div>
                </form>
            </div>

            <div class="card" style="margin-top:1rem">
                <h3>ДЗ для занятия (всем студентам группы)</h3>
                <form class="form-stack" onsubmit="Pages.onAssignHwForSession(event)">
                    <label>Занятие</label>
                    <select name="scheduleId" required>
                        <option value="">Выберите занятие</option>
                        ${schedule.map(s => {
                            const group = groupById[s.groupId];
                            const course = courseById[s.courseId];
                            return `<option value="${s.id}" data-course="${s.courseId}" data-group="${s.groupId}">${s.weekday||s.date||''} ${s.time||''} — ${course ? course.title : s.courseId} (${group ? group.name : s.groupId})</option>`;
                        }).join('')}
                    </select>
                    <label>Тема</label>
                    <input name="title" required>
                    <label>Описание</label>
                    <textarea name="description" rows="3"></textarea>
                    <div style="margin-top:.5rem"><button class="btn" type="submit">Выдать ДЗ</button></div>
                </form>
            </div>
        </section>
    `;
}

	function pageAdminManage() {
		const user = DataStore.currentUser();
		if (!user) return redirectLogin();
		if (user.role !== 'Администратор') return `<div class="card">Недостаточно прав</div>`;
		const users = DataStore.getUsers();
		const groups = DataStore.getGroups();
		const db = (JSON.parse(localStorage.getItem('top-academy-db'))||{});
		const schedule = db.schedule || [];
		const courses = db.courses || [];
		const predefinedLevels = ['Средний'];
		const extraLevels = Array.from(new Set(courses.map(c => c.level).filter(Boolean)))
			.filter(level => !predefinedLevels.includes(level));
		const groupById = Object.fromEntries(groups.map(g => [g.id, g]));
		const courseById = Object.fromEntries(courses.map(c => [c.id, c]));
		const userById = Object.fromEntries(users.map(u => [u.id, u]));
		
		return `
			<section>
				<h2>Управление (администратор)</h2>
				<div class="admin-intro">
					<div class="card admin-auto-save">
						<h3>🔄 Автоматическое сохранение</h3>
						<p>Все изменения мгновенно записываются в localStorage браузера и берутся оттуда при каждой загрузке страницы.</p>
						<div class="auto-save-actions">
							<button class="btn green" type="button" onclick="Pages.forceReloadData()">Сбросить из BD.json</button>
							<button class="btn ghost" type="button" onclick="Pages.downloadBD()">Скачать текущие данные</button>
						</div>
					</div>
					<div class="card admin-stats">
						<div class="stat-chip blue">
							<div class="stat-label">Пользователи</div>
							<div class="stat-value">${users.length}</div>
						</div>
						<div class="stat-chip green">
							<div class="stat-label">Преподаватели</div>
							<div class="stat-value">${users.filter(u => u.role === 'Преподаватель').length}</div>
						</div>
						<div class="stat-chip orange">
							<div class="stat-label">Студенты</div>
							<div class="stat-value">${users.filter(u => u.role === 'Студент').length}</div>
						</div>
						<div class="stat-chip purple">
							<div class="stat-label">Группы</div>
							<div class="stat-value">${groups.length}</div>
						</div>
						<div class="stat-chip">
							<div class="stat-label">Занятия</div>
							<div class="stat-value">${schedule.length}</div>
						</div>
					</div>
				</div>
				<div class="admin-columns">
					<div class="admin-stack">
						<div class="card admin-section">
						<h3>Группы</h3>
						<div class="table-wrapper">
							<table class="table"><thead><tr><th>Название</th><th>Курс</th><th>Преподаватель</th><th>Студенты</th><th></th></tr></thead>
							<tbody>
								${groups.map(g => {
									const course = courseById[g.courseId] || {};
									const teacher = userById[g.teacherId] || {};
									const students = DataStore.getStudentsInGroup(g.id);
								return `<tr>
									<td>${g.name}</td>
									<td>${course.title || g.courseId}</td>
									<td>${teacher.name || 'Не назначен'}</td>
									<td>${students.length}</td>
									<td><button class="btn danger btn-icon" type="button" aria-label="Удалить группу" title="Удалить" onclick="Pages.onRemoveGroup('${g.id}')">🗑</button></td>
								</tr>`;
								}).join('')}
							</tbody></table>
						</div>
						<form class="form-stack" onsubmit="Pages.onAddGroup(event)" style="margin-top:.5rem">
							<div class="row">
								<input name="name" placeholder="Название группы" required>
								<select name="courseId" required>
									<option value="">Выберите курс</option>
									${courses.map(c => `<option value="${c.id}">${c.title}</option>`).join('')}
								</select>
							</div>
							<div class="row">
								<select name="teacherId" required>
									<option value="">Выберите преподавателя</option>
									${users.filter(u => u.role === 'Преподаватель').map(u => `<option value="${u.id}">${u.name}</option>`).join('')}
								</select>
							</div>
							<textarea name="description" rows="2" placeholder="Описание группы"></textarea>
							<div style="margin-top:.5rem"><button class="btn" type="submit">Создать группу</button></div>
						</form>
					</div>
					<div class="card admin-section">
						<h3>Назначение студентов в группы</h3>
						<div class="admin-filters">
							<input id="adminUsersSearch" class="admin-filter" type="search" placeholder="Поиск пользователя..." oninput="Pages.onAdminFilterUsers()">
							<select id="adminUsersRole" class="admin-filter" onchange="Pages.onAdminFilterUsers()">
								<option value="">Все роли</option>
								<option value="Студент">Студенты</option>
								<option value="Преподаватель">Преподаватели</option>
								<option value="Администратор">Администраторы</option>
							</select>
						</div>
						<div class="table-wrapper">
							<table class="table" id="adminUsersTable"><thead><tr><th>Имя</th><th>Роль</th><th>Email</th><th>Группы</th><th>Действия</th></tr></thead>
							<tbody>
								${users.map(u => {
									const studentGroups = (u.groups || []).map(gId => {
										const group = groupById[gId];
										return group ? `<span class="pill">${group.name}</span>` : '';
									}).join(' ');
									const availableGroups = groups.filter(g => !(u.groups || []).includes(g.id)).map(g => `<option value="${g.id}">${g.name}</option>`).join('');
									const actions = u.role === 'Студент'
										? `<select class="assign-select" onchange="Pages.onAssignStudentToGroup('${u.id}', this.value)">
											<option value="">Добавить в группу</option>${availableGroups}</select>`
										: '';
									return `<tr data-role="${u.role}" data-name="${u.name.toLowerCase()}" data-email="${u.email.toLowerCase()}">
										<td>${u.name}</td>
										<td>${u.role}</td>
										<td>${u.email}</td>
										<td>${studentGroups}</td>
										<td>
											${actions}
											${u.role!=='Администратор'?`<button class="btn danger btn-icon" type="button" aria-label="Удалить пользователя" title="Удалить" onclick="Pages.onRemoveUser('${u.id}')">🗑</button>`:''}
										</td>
									</tr>`;
								}).join('')}
							</tbody></table>
						</div>
						<div style="margin-top:.5rem"><a class="btn ghost" href="#/register">Добавить пользователя</a></div>
					</div>
					</div>
					<div class="admin-stack">
						<div class="card admin-section">
						<h3>Расписание</h3>
						<div class="admin-filters">
							<input id="adminScheduleSearch" class="admin-filter" type="search" placeholder="Поиск занятия..." oninput="Pages.onAdminFilterSchedule()">
							<select id="adminScheduleCourse" class="admin-filter" onchange="Pages.onAdminFilterSchedule()">
								<option value="">Все курсы</option>
								${courses.map(c => `<option value="${c.id}">${c.title}</option>`).join('')}
							</select>
						</div>
						<div class="table-wrapper">
							<table class="table" id="adminScheduleTable"><thead><tr><th>День</th><th>Время</th><th>Курс</th><th>Группа</th><th>Ауд.</th><th></th></tr></thead>
							<tbody>
								${schedule.map(s => {
									const group = groupById[s.groupId];
									return `<tr data-day="${(s.weekday||'').toLowerCase()}" data-time="${(s.time||'').toLowerCase()}" data-course="${(s.courseId||'').toLowerCase()}">
										<td>${s.weekday}</td>
										<td>${s.time}</td>
										<td>${s.courseId}</td>
										<td>${group ? group.name : s.groupId}</td>
										<td>${s.room}</td>
										<td><button class="btn danger btn-icon" type="button" aria-label="Удалить занятие" title="Удалить" onclick="Pages.onRemoveSchedule('${s.id}')">🗑</button></td>
									</tr>`;
								}).join('')}
							</tbody></table>
						</div>
						<form class="form-stack" onsubmit="Pages.onAddSchedule(event)" style="margin-top:.5rem">
							<div class="row">
								<input name="weekday" placeholder="День" required>
								<input name="time" placeholder="Время" required>
							</div>
							<div class="row">
								<select name="courseId" required>
									<option value="">Выберите курс</option>
									${courses.map(c => `<option value="${c.id}">${c.title}</option>`).join('')}
								</select>
								<select name="groupId" required>
									<option value="">Выберите группу</option>
									${groups.map(g => `<option value="${g.id}">${g.name}</option>`).join('')}
								</select>
							</div>
							<input name="room" placeholder="Аудитория" required>
							<div style="margin-top:.5rem"><button class="btn" type="submit">Добавить</button></div>
						</form>
					</div>
					<div class="card admin-section">
						<h3>Курсы</h3>
						<div class="admin-filters">
							<input id="adminCoursesSearch" class="admin-filter" type="search" placeholder="Поиск курса..." oninput="Pages.onAdminFilterCourses()">
							<select id="adminCoursesLevel" class="admin-filter" onchange="Pages.onAdminFilterCourses()">
								<option value="">Все уровни</option>
								<option value="Средний">Средний уровень сложности</option>
								${extraLevels.map(level => `<option value="${level}">${level}</option>`).join('')}
							</select>
						</div>
						<div class="table-wrapper">
							<table class="table" id="adminCoursesTable"><thead><tr><th>ID</th><th>Название</th><th>Уровень</th><th>Длит.</th><th></th></tr></thead>
							<tbody>
								${courses.map(c => `<tr data-level="${(c.level||'').toLowerCase()}" data-title="${c.title.toLowerCase()}" data-tags="${(c.tags||[]).join(' ').toLowerCase()}">
									<td>${c.id}</td>
									<td>${c.title}</td>
									<td>${c.level||''}</td>
									<td>${c.duration||''}</td>
									<td><button class="btn danger btn-icon" type="button" aria-label="Удалить курс" title="Удалить" onclick="Pages.onRemoveCourse('${c.id}')">🗑</button></td>
								</tr>`).join('')}
							</tbody></table>
						</div>
						<form class="form-stack" onsubmit="Pages.onAddCourse(event)">
							<div class="row">
								<input name="id" placeholder="id курса (латиница)" required>
								<input name="title" placeholder="Название" required>
							</div>
							<div class="row">
								<input name="level" placeholder="Уровень (Начальный/Средний/Продвинутый)">
								<input name="duration" placeholder="Длительность (недели)">
							</div>
							<input name="tags" placeholder="Теги через запятую">
							<textarea name="description" rows="3" placeholder="Описание"></textarea>
							<div style="margin-top:.5rem"><button class="btn" type="submit">Добавить курс</button></div>
						</form>
					</div>
					</div>
				</div>
			</section>
		`;
	}

	function redirectLogin() { location.hash = '#/login'; return ''; }

	function pageNotFound() {
		return `<div class="card"><h3>Страница не найдена</h3><p class="muted">Проверьте адрес или воспользуйтесь навигацией.</p></div>`;
	}

	function onCourseSearch(value) {
		const q = value.toLowerCase();
		DataStore.load().then(data => {
			const level = document.getElementById('level')?.value || '';
			const filtered = (data.courses||[]).filter(c =>
				(!level || (c.level||'').toLowerCase() === level.toLowerCase()) &&
				(!q || c.title.toLowerCase().includes(q) || (c.tags||[]).join(' ').toLowerCase().includes(q))
			);
			document.getElementById('coursesList').innerHTML = filtered.map(UI.courseCard).join('') || UI.emptyState('Ничего не найдено');
		});
	}
	function onCourseFilterLevel(value) { onCourseSearch(document.getElementById('q')?.value || ''); }

	function onProfileSave(e) {
		e.preventDefault();
		const form = e.target;
		const currentRole = DataStore.currentUser()?.role || DataStore.getProfile().role;
		const profile = { name: form.name.value.trim(), email: form.email.value.trim(), role: currentRole };
		DataStore.saveProfile(profile);
		alert('Профиль сохранён');
	}
	function onProfileReset() {
		DataStore.saveProfile({ role: 'Студент', name: '', email: '' });
		Router.render();
	}

	function onFeedbackSubmit(e) {
		e.preventDefault();
		const form = e.target;
		const entry = { topic: form.topic.value.trim(), course: form.course.value.trim(), message: form.message.value.trim() };
		if (!entry.topic || !entry.message) return;
		DataStore.upsertFeedback(entry);
		form.reset();
		const toast = document.getElementById('feedbackToast');
		toast.classList.remove('sr-only');
		toast.textContent = 'Спасибо! Сообщение отправлено.';
		setTimeout(() => { toast.classList.add('sr-only'); toast.textContent = ''; }, 2500);
	}

	async function onLogin(e) {
		e.preventDefault();
		const f = e.target; try {
			await DataStore.login(f.email.value.trim(), f.password.value.trim());
			Router.render();
			location.hash = '#/';
		} catch(err){ alert(err.message); }
	}
	async function onRegister(e) {
		e.preventDefault();
		const f = e.target;
		const password = f.password.value || '';
		if (password.length < 6) {
			alert('Пароль должен содержать минимум 6 символов.');
			return;
		}
		try {
			await DataStore.addUser({ name: f.name.value.trim(), email: f.email.value.trim(), password });
			alert('Аккаунт создан. Войдите, используя ваш email и пароль.');
			location.hash = '#/login';
		} catch(err){ alert(err.message); }
	}
	function onLogout() { DataStore.logout(); Router.render(); }

	function openAssignHw(studentId) {
		document.getElementById('hwStudentId').value = studentId;
		document.getElementById('hwModal').style.display = 'block';
	}
	function closeAssignHw() { document.getElementById('hwModal').style.display = 'none'; }
	async function onAssignHw(e) {
		e.preventDefault();
		const f = e.target;
		await DataStore.assignHomework({ studentId: f.studentId.value, courseId: f.courseId.value, groupId: f.groupId.value, title: f.title.value, description: f.description.value });
		closeAssignHw();
		alert('Задание создано');
	}
	async function onRemoveUser(id) { if (confirm('Удалить пользователя?')) { await DataStore.removeUser(id); Router.render(); } }
	async function onAddSchedule(e) { e.preventDefault(); const f=e.target; await DataStore.addSchedule({ weekday:f.weekday.value, time:f.time.value, courseId:f.courseId.value, groupId:f.groupId.value, room:f.room.value }); Router.render(); }
	async function onRemoveSchedule(id) { if (confirm('Удалить занятие?')) { await DataStore.removeSchedule(id); Router.render(); } }
	async function onSetRole(id, role) { await DataStore.setUserRole(id, role); }
	async function onAddCourse(e) { e.preventDefault(); const f=e.target; try{ await DataStore.addCourse({ id:f.id.value.trim(), title:f.title.value.trim(), level:f.level.value.trim(), duration:f.duration.value.trim(), tags:f.tags.value.trim(), description:f.description.value.trim() }); alert('Курс добавлен'); Router.render(); } catch(err){ alert(err.message); } }
	async function onRemoveCourse(id) { if (confirm('Удалить курс?')) { await DataStore.removeCourse(id); Router.render(); } }
async function onSetGrades(e) { e.preventDefault(); const f=e.target; const scheduleId=f.scheduleId.value; const users=DataStore.getUsers().filter(u=>u.role==='Студент'); for(const s of users){ const val=f[`grade_${s.id}`]?.value; if (val) await DataStore.setSessionGrade({ scheduleId, studentId:s.id, grade:Number(val) }); } alert('Оценки сохранены'); }
async function onAssignHwForSession(e) { e.preventDefault(); const f=e.target; const scheduleId=f.scheduleId.value; const courseId=(f.scheduleId.selectedOptions[0].dataset.course); const groupId=(f.scheduleId.selectedOptions[0].dataset.group); await DataStore.assignHomeworkForSession({ scheduleId, courseId, groupId, title:f.title.value.trim(), description:f.description.value.trim() }); alert('ДЗ выдано всем студентам группы'); f.reset(); }

	function escapeHtml(s) { return String(s||'').replace(/[&<>"]+/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[ch])); }

	Router.on('/', pageHome);
	Router.on('/courses', pageCourses);
	Router.on('/schedule', pageSchedule);
	Router.on('/account', pageAccount);
	Router.on('/login', pageLogin);
	Router.on('/register', pageRegister);
	Router.on('/homework', pageStudentHomework);
	Router.on('/students', pageTeacherStudents);
	Router.on('/admin', pageAdminManage);
	Router.on('/feedback', pageFeedback);
	Router.otherwise(pageNotFound);

	async function onAddGroup(e) {
		e.preventDefault();
		const f = e.target;
		try {
			await DataStore.addGroup({
				name: f.name.value.trim(),
				courseId: f.courseId.value,
				teacherId: f.teacherId.value,
				description: f.description.value.trim()
			});
			alert('Группа создана');
			Router.render();
		} catch(err) {
			alert(err.message);
		}
	}
	async function onRemoveGroup(groupId) {
		if (confirm('Удалить группу?')) {
			await DataStore.removeGroup(groupId);
			Router.render();
		}
	}
	async function onAssignStudentToGroup(studentId, groupId) {
		if (groupId) {
			await DataStore.assignUserToGroup(studentId, groupId);
			Router.render();
		}
	}

	async function forceReloadData() {
		try {
			await DataStore.forceReloadFromJson();
			alert('Данные успешно перезагружены из BD.json!');
			Router.render();
		} catch (error) {
			alert('Ошибка при перезагрузке данных: ' + error.message);
		}
	}

	async function downloadBD() {
		try {
			const data = await DataStore.load();
			const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
			const url = URL.createObjectURL(blob);
			const a = document.createElement('a');
			a.href = url;
			a.download = 'BD.json';
			a.style.display = 'none';
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
			URL.revokeObjectURL(url);
			alert('✅ Файл BD.json скачан! Замените старый файл новым.');
		} catch (error) {
			alert('Ошибка при скачивании: ' + error.message);
		}
	}

	function onAdminFilterUsers() {
		const search = document.getElementById('adminUsersSearch')?.value.trim().toLowerCase() || '';
		const role = document.getElementById('adminUsersRole')?.value || '';
		document.querySelectorAll('#adminUsersTable tbody tr').forEach(row => {
			const rowRole = row.getAttribute('data-role') || '';
			const matchRole = !role || rowRole === role;
			const text = `${row.getAttribute('data-name')||''} ${row.getAttribute('data-email')||''}`;
			const matchText = !search || text.includes(search);
			row.style.display = matchRole && matchText ? '' : 'none';
		});
	}

	function onAdminFilterSchedule() {
		const search = document.getElementById('adminScheduleSearch')?.value.trim().toLowerCase() || '';
		const course = (document.getElementById('adminScheduleCourse')?.value || '').toLowerCase();
		document.querySelectorAll('#adminScheduleTable tbody tr').forEach(row => {
			const rowCourse = row.getAttribute('data-course') || '';
			const day = row.getAttribute('data-day') || '';
			const time = row.getAttribute('data-time') || '';
			const matchCourse = !course || rowCourse === course;
			const matchSearch = !search || rowCourse.includes(search) || day.includes(search) || time.includes(search);
			row.style.display = matchCourse && matchSearch ? '' : 'none';
		});
	}

	function onAdminFilterCourses() {
		const search = document.getElementById('adminCoursesSearch')?.value.trim().toLowerCase() || '';
		const level = (document.getElementById('adminCoursesLevel')?.value || '').toLowerCase();
		document.querySelectorAll('#adminCoursesTable tbody tr').forEach(row => {
			const rowLevel = row.getAttribute('data-level') || '';
			const rowTitle = row.getAttribute('data-title') || '';
			const rowTags = row.getAttribute('data-tags') || '';
			const matchLevel = !level || rowLevel === level;
			const text = `${rowTitle} ${rowTags}`;
			const matchText = !search || text.includes(search);
			row.style.display = matchLevel && matchText ? '' : 'none';
		});
	}

	window.Pages = { onCourseSearch, onCourseFilterLevel, onProfileSave, onProfileReset, onFeedbackSubmit, onLogin, onRegister, onLogout, openAssignHw, closeAssignHw, onAssignHw, onRemoveUser, onAddSchedule, onRemoveSchedule, onSetRole, onAddCourse, onRemoveCourse, onSetGrades, onAssignHwForSession, onAddGroup, onRemoveGroup, onAssignStudentToGroup, forceReloadData, downloadBD, onAdminFilterUsers, onAdminFilterSchedule, onAdminFilterCourses };
})();


