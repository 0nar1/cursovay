const UI = (function() {
	function courseCard(course) {
		const tags = (course.tags || []).map(t => `<span class="pill">${t}</span>`).join(' ');
		return `
			<div class="card">
				<h3>${course.title}</h3>
				<p class="muted">Уровень: ${course.level} • ${course.duration} недель</p>
				<p>${course.description || ''}</p>
				<div>${tags}</div>
			</div>
		`;
	}

function scheduleRow(entry, coursesById, groupsById) {
		const course = coursesById[entry.courseId];
		const groupName = (groupsById && groupsById[entry.groupId]?.name) || entry.group || entry.groupId || '';
		return `
			<tr>
				<td>${entry.weekday}</td>
				<td>${entry.time}</td>
				<td>${course?.title || entry.courseId}</td>
				<td>${groupName}</td>
				<td>${entry.room}</td>
			</tr>
		`;
	}

	function emptyState(text) { return `<div class="empty">${text}</div>`; }

	return { courseCard, scheduleRow, emptyState };
})();




