const DataStore = (function() {
	const LOCAL_KEY = 'top-academy-db';
	const SESSION_KEY = 'top-academy-current-user-id';

	async function fetchInitialData() {
		try {
			const res = await fetch('BD.json', { cache: 'no-cache' });
			if (!res.ok) throw new Error('BD.json not found');
			return await res.json();
		} catch (e) {
			console.warn('BD.json не найден, используется дефолтный набор:', e.message);
			return defaultData();
		}
	}

	function defaultData() {
		return {
			courses: [
				{ id: 'js-basic', title: 'JavaScript Базовый', level: 'Начальный', duration: 10, tags: ['JS','Web'], description: 'Основы JS и DOM.' },
				{ id: 'js-adv', title: 'JavaScript Продвинутый', level: 'Продвинутый', duration: 12, tags: ['JS','Patterns'], description: 'Асинхронность, архитектура.' },
				{ id: 'python', title: 'Python для начинающих', level: 'Начальный', duration: 10, tags: ['Python','Data'], description: 'Синтаксис, ООП, файлы.' }
			],
			groups: [
				{ id: 'g-js-101', name: 'JS-101', courseId: 'js-basic', teacherId: 'u-teacher', description: 'Группа JavaScript для начинающих' },
				{ id: 'g-py-101', name: 'PY-101', courseId: 'python', teacherId: 'u-teacher', description: 'Группа Python для начинающих' },
				{ id: 'g-js-201', name: 'JS-201', courseId: 'js-adv', teacherId: 'u-teacher', description: 'Продвинутая группа JavaScript' }
			],
			schedule: [
				{ id: 's1', courseId: 'js-basic', groupId: 'g-js-101', weekday: 'Пн', time: '18:30', room: 'Ауд. 1' },
				{ id: 's2', courseId: 'python', groupId: 'g-py-101', weekday: 'Ср', time: '19:00', room: 'Ауд. 2' },
				{ id: 's3', courseId: 'js-adv', groupId: 'g-js-201', weekday: 'Пт', time: '18:30', room: 'Ауд. 1' }
			],
			users: [
				{ id: 'u-admin', name: 'Администратор', email: 'admin@top.local', role: 'Администратор', password: 'admin' },
				{ id: 'u-teacher', name: 'Преподаватель', email: 'teacher@top.local', role: 'Преподаватель', password: 'teacher', assignedGroups: ['g-js-101', 'g-py-101', 'g-js-201'] },
				{ id: 'u-student', name: 'Студент', email: 'student@top.local', role: 'Студент', password: 'student', groups: ['g-js-101'] }
			],
			homework: [
			]
		};
	}

	async function load() {
		let data = null;
		const cached = localStorage.getItem(LOCAL_KEY);
		if (cached) {
			try { data = JSON.parse(cached); } catch {}
		}
		if (!data) {
			data = await fetchInitialData();
			localStorage.setItem(LOCAL_KEY, JSON.stringify(data));
		}
		if (!Array.isArray(data.homework)) data.homework = [];
		if (!Array.isArray(data.groups)) data.groups = [];
		if (!Array.isArray(data.grades)) data.grades = [];
		return data;
	}

	async function forceReloadFromJson() {
		try {
			const data = await fetchInitialData();
			localStorage.setItem(LOCAL_KEY, JSON.stringify(data));
			return data;
		} catch (e) {
			console.error('Error loading from BD.json:', e);
			return null;
		}
	}

	async function save(data) { 
		localStorage.setItem(LOCAL_KEY, JSON.stringify(data));
	}

	function getUsers() {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		return data.users || [];
	}
	async function addUser(user) {
		const data = await load();
		const exists = (data.users||[]).some(u => u.email.toLowerCase() === user.email.toLowerCase());
		if (exists) throw new Error('Пользователь с таким email уже существует');
		if (!user.password || user.password.length < 6) {
			throw new Error('Пароль должен содержать минимум 6 символов.');
		}
		const id = crypto.randomUUID?.() || ('u-' + Date.now());
		const newUser = { id, role: 'Студент', ...user };
		data.users = data.users || [];
		data.users.push(newUser);
		await save(data);
		return newUser;
	}
	async function removeUser(userId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		data.users = (data.users||[]).filter(u => u.id !== userId);
		await save(data);
	}
	async function setUserRole(userId, role) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		const u = (data.users||[]).find(x => x.id === userId);
		if (u) { u.role = role; await save(data); }
		return u;
	}
	async function login(email, password) {
		const users = (await load()).users || [];
		const user = users.find(u => u.email.toLowerCase() === email.toLowerCase() && u.password === password);
		if (!user) throw new Error('Неверный email или пароль');
		localStorage.setItem(SESSION_KEY, user.id);
		return user;
	}
	function logout() { localStorage.removeItem(SESSION_KEY); }
	function currentUser() {
		const id = localStorage.getItem(SESSION_KEY);
		if (!id) return null;
		return getUsers().find(u => u.id === id) || null;
	}

	function listHomeworkForStudent(studentId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		return (data.homework||[]).filter(h => h.studentId === studentId);
	}
	async function assignHomework({ studentId, courseId, groupId, title, description }) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		const item = { id: crypto.randomUUID?.() || ('h-' + Date.now()), studentId, courseId, groupId, title, description, grade: null };
		data.homework = data.homework || [];
		data.homework.push(item);
		await save(data);
		return item;
	}
	async function setGrade(homeworkId, grade) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		const hw = (data.homework||[]).find(h => h.id === homeworkId);
		if (hw) { hw.grade = grade; await save(data); }
		return hw;
	}

	async function setSessionGrade({ scheduleId, studentId, grade }) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		data.grades = data.grades || [];
		const key = `${scheduleId}:${studentId}`;
		const rec = data.grades.find(g => g.key === key);
		if (rec) rec.grade = grade; else data.grades.push({ key, scheduleId, studentId, grade });
		await save(data);
		return grade;
	}

	function getSessionGrades(scheduleId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		return (data.grades||[]).filter(g => g.scheduleId === scheduleId);
	}

	function getGroups() {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		return data.groups || [];
	}
	async function addGroup(group) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		data.groups = data.groups || [];
		const id = crypto.randomUUID?.() || ('g-' + Date.now());
		const newGroup = { id, ...group };
		data.groups.push(newGroup);
		await save(data);
		return newGroup;
	}
	async function removeGroup(groupId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		data.groups = (data.groups||[]).filter(g => g.id !== groupId);
		data.users = (data.users||[]).map(u => ({
			...u,
			groups: (u.groups||[]).filter(g => g !== groupId),
			assignedGroups: (u.assignedGroups||[]).filter(g => g !== groupId)
		}));
		await save(data);
	}
	async function assignUserToGroup(userId, groupId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		const user = (data.users||[]).find(u => u.id === userId);
		if (user) {
			user.groups = user.groups || [];
			if (!user.groups.includes(groupId)) {
				user.groups.push(groupId);
				await save(data);
			}
		}
		return user;
	}
	async function removeUserFromGroup(userId, groupId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		const user = (data.users||[]).find(u => u.id === userId);
		if (user) {
			user.groups = (user.groups||[]).filter(g => g !== groupId);
			await save(data);
		}
		return user;
	}
	async function assignTeacherToGroup(teacherId, groupId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		const teacher = (data.users||[]).find(u => u.id === teacherId);
		if (teacher) {
			teacher.assignedGroups = teacher.assignedGroups || [];
			if (!teacher.assignedGroups.includes(groupId)) {
				teacher.assignedGroups.push(groupId);
				await save(data);
			}
		}
		return teacher;
	}
	function getStudentsInGroup(groupId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		return (data.users||[]).filter(u => u.role === 'Студент' && (u.groups||[]).includes(groupId));
	}
	function getGroupsForStudent(studentId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		const student = (data.users||[]).find(u => u.id === studentId);
		return student ? (student.groups||[]) : [];
	}
	function getGroupsForTeacher(teacherId) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		const teacher = (data.users||[]).find(u => u.id === teacherId);
		const direct = teacher ? (teacher.assignedGroups||[]) : [];
		const fromGroups = (data.groups||[])
			.filter(g => g.teacherId === teacherId)
			.map(g => g.id);
		return Array.from(new Set([...direct, ...fromGroups]));
	}

	async function assignHomeworkForSession({ scheduleId, courseId, groupId, title, description }) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		const students = getStudentsInGroup(groupId);
		data.homework = data.homework || [];
		students.forEach(s => {
			data.homework.push({ id: crypto.randomUUID?.() || ('h-' + Date.now() + Math.random()), studentId: s.id, courseId, groupId, scheduleId, title, description, grade: null });
		});
		await save(data);
		return true;
	}

	async function addSchedule(entry) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		data.schedule.push({ id: crypto.randomUUID?.() || ('s-' + Date.now()), ...entry });
		await save(data);
	}
	async function removeSchedule(id) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		data.schedule = data.schedule.filter(s => s.id !== id);
		await save(data);
	}

	async function addCourse(course) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		data.courses = data.courses || [];
		const exists = data.courses.some(c => c.id === course.id);
		if (exists) throw new Error('Курс с таким id уже существует');
		data.courses.push({ id: course.id, title: course.title, level: course.level || 'Начальный', duration: Number(course.duration)||course.duration||0, tags: (course.tags||'').split(',').map(t=>t.trim()).filter(Boolean), description: course.description||'' });
		await save(data);
	}
	async function removeCourse(id) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		data.courses = (data.courses||[]).filter(c => c.id !== id);
		await save(data);
	}

	async function upsertFeedback(entry) {
		const data = JSON.parse(localStorage.getItem(LOCAL_KEY));
		if (!data.feedback) data.feedback = [];
		data.feedback.push({ id: crypto.randomUUID?.() || String(Date.now()), ts: Date.now(), ...entry });
		await save(data);
		return data.feedback[data.feedback.length - 1];
	}

	function saveProfile(profile) {
		localStorage.setItem('top-academy-profile', JSON.stringify(profile));
		return profile;
	}
	function getProfile() {
		const raw = localStorage.getItem('top-academy-profile');
		return raw ? JSON.parse(raw) : { role: 'Студент', name: '', email: '' };
	}

	return { 
		load, save, upsertFeedback, getProfile, saveProfile, forceReloadFromJson,
		getUsers, addUser, removeUser, setUserRole, login, logout, currentUser, 
		listHomeworkForStudent, assignHomework, setGrade, setSessionGrade, getSessionGrades, assignHomeworkForSession, 
		addSchedule, removeSchedule, addCourse, removeCourse,
		getGroups, addGroup, removeGroup, assignUserToGroup, removeUserFromGroup, assignTeacherToGroup,
		getStudentsInGroup, getGroupsForStudent, getGroupsForTeacher
	};
})();


