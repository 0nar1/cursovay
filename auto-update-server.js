const fs = require('fs');
const path = require('path');
const http = require('http');

const PORT = 8001;
const BD_FILE = 'BD.json';

const server = http.createServer((req, res) => {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
    
    if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
    }
    
    if (req.method === 'GET') {
        try {
            const data = fs.readFileSync(BD_FILE, 'utf8');
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(data);
            console.log('📖 BD.json прочитан');
        } catch (e) {
            res.writeHead(404, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({ error: 'BD.json не найден' }));
        }
    } else if (req.method === 'POST') {
        let body = '';
        req.on('data', chunk => {
            body += chunk.toString();
        });
        
        req.on('end', () => {
            try {
                const data = JSON.parse(body);
                
                fs.writeFileSync(BD_FILE, JSON.stringify(data, null, 2), 'utf8');
                
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ status: 'success', message: 'BD.json обновлен' }));
                
                console.log('✅ BD.json автоматически обновлен');
                console.log(`📊 Пользователей: ${data.users?.length || 0}`);
                console.log(`📚 Курсов: ${data.courses?.length || 0}`);
                console.log(`👥 Групп: ${data.groups?.length || 0}`);
                console.log(`📅 Занятий: ${data.schedule?.length || 0}`);
                
            } catch (e) {
                res.writeHead(400, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ error: 'Неверный JSON: ' + e.message }));
                console.log('❌ Ошибка сохранения BD.json:', e.message);
            }
        });
    } else {
        res.writeHead(405, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: 'Метод не поддерживается' }));
    }
});

server.listen(PORT, () => {
    console.log(`🚀 Сервер автоматического обновления BD.json запущен на порту ${PORT}`);
    console.log(`📁 Работает с файлом: ${path.resolve(BD_FILE)}`);
    console.log(`🌐 API: http://localhost:${PORT}`);
    console.log('🔄 Все изменения на сайте автоматически сохраняются в BD.json');
    console.log('Нажмите Ctrl+C для остановки');
});

process.on('uncaughtException', (err) => {
    console.error('❌ Критическая ошибка:', err);
});

process.on('SIGINT', () => {
    console.log('\n🛑 Сервер остановлен');
    process.exit(0);
});
