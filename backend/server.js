const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
const dotenv = require('dotenv');
const path = require('path');
const fs = require('fs');
const jwt = require('jsonwebtoken');
const cookieParser = require('cookie-parser');

// Load environment variables
dotenv.config();

const app = express();
const PORT = process.env.PORT || 5000;
const FRONTEND_URL = process.env.FRONTEND_URL || 'http://localhost:3000';
const JWT_SECRET = process.env.JWT_SECRET || 'supersecretadminjwt';

app.use(cors({
  origin: FRONTEND_URL,
  credentials: true,
}));
app.use(cookieParser());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Serve static images
app.use('/api/images', express.static(path.join(__dirname, '../assets/uploads')));

// Database connection
let db;
async function connectDB() {
  try {
    db = await mysql.createConnection({
      host: process.env.DB_HOST || 'localhost',
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASS || '',
      database: process.env.DB_NAME || 'smartuae',
      charset: 'utf8mb4'
    });
    console.log('Connected to MySQL database');
  } catch (error) {
    console.error('Database connection failed:', error);
    process.exit(1);
  }
}

// Authentication helpers
function requireAdmin(req, res, next) {
  const token = req.cookies?.admin_token;
  if (!token) {
    return res.status(401).json({ error: 'Unauthorized' });
  }

  try {
    req.admin = jwt.verify(token, JWT_SECRET);
    return next();
  } catch (error) {
    return res.status(401).json({ error: 'Unauthorized' });
  }
}

app.post('/api/admin/login', async (req, res) => {
  try {
    const { username, password } = req.body;
    if (!username || !password) {
      return res.status(400).json({ error: 'Missing credentials' });
    }

    const [rows] = await db.execute('SELECT * FROM admin_users WHERE username = ?', [username]);
    const user = rows[0];

    if (!user) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }

    const bcrypt = require('bcryptjs');
    const validPassword = await bcrypt.compare(password, user.password);
    if (!validPassword) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }

    const token = jwt.sign({ id: user.id, username: user.username }, JWT_SECRET, {
      expiresIn: '8h',
    });

    res.cookie('admin_token', token, {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      sameSite: 'lax',
      maxAge: 8 * 60 * 60 * 1000,
    });

    res.json({ success: true });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/admin/logout', (req, res) => {
  res.clearCookie('admin_token', { httpOnly: true, sameSite: 'lax' });
  res.json({ success: true });
});

app.get('/api/admin/me', (req, res) => {
  const token = req.cookies?.admin_token;
  if (!token) {
    return res.status(401).json({ error: 'Unauthorized' });
  }

  try {
    const user = jwt.verify(token, JWT_SECRET);
    return res.json({ user });
  } catch (error) {
    return res.status(401).json({ error: 'Unauthorized' });
  }
});

function createAdminCrudRoutes(resource, table, columns) {
  const basePath = `/api/admin/${resource}`;

  app.get(basePath, requireAdmin, async (req, res) => {
    try {
      const [rows] = await db.execute(`SELECT * FROM ${table} ORDER BY id DESC`);
      res.json(rows);
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  app.get(`${basePath}/:id`, requireAdmin, async (req, res) => {
    try {
      const [rows] = await db.execute(`SELECT * FROM ${table} WHERE id = ?`, [req.params.id]);
      if (rows.length === 0) {
        return res.status(404).json({ error: 'Not found' });
      }
      res.json(rows[0]);
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  app.post(basePath, requireAdmin, async (req, res) => {
    try {
      const values = columns.map((column) => req.body[column] ?? null);
      const placeholders = columns.map(() => '?').join(', ');
      await db.execute(`INSERT INTO ${table} (${columns.join(', ')}) VALUES (${placeholders})`, values);
      res.json({ success: true });
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  app.put(`${basePath}/:id`, requireAdmin, async (req, res) => {
    try {
      const values = columns.map((column) => req.body[column] ?? null);
      const setClause = columns.map((column) => `${column} = ?`).join(', ');
      await db.execute(`UPDATE ${table} SET ${setClause} WHERE id = ?`, [...values, req.params.id]);
      res.json({ success: true });
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });

  app.delete(`${basePath}/:id`, requireAdmin, async (req, res) => {
    try {
      await db.execute(`DELETE FROM ${table} WHERE id = ?`, [req.params.id]);
      res.json({ success: true });
    } catch (error) {
      res.status(500).json({ error: error.message });
    }
  });
}

// Admin CRUD endpoints
createAdminCrudRoutes('sliders', 'sliders', ['title','subtitle','button_text','button_link','image','sort_order','is_active']);
createAdminCrudRoutes('main-categories', 'main_categories', ['name','icon','link','sort_order','is_active']);
createAdminCrudRoutes('popular-categories', 'popular_categories', ['name','image','link','sort_order','is_active']);
createAdminCrudRoutes('business-categories', 'business_categories', ['name','icon','group_name','sort_order','is_active']);
createAdminCrudRoutes('businesses', 'businesses', ['name','category_id','description','image','rating','distance','address','phone','whatsapp','is_active']);
createAdminCrudRoutes('classified-categories', 'classified_categories', ['name','icon','sort_order','is_active']);
createAdminCrudRoutes('classified-sections', 'classified_sections', ['name','sort_order','is_active']);
createAdminCrudRoutes('classifieds', 'classifieds', ['title','description','price','currency','category_id','section_id','image','location','age','model','warranty','color','brand','condition_status','version','storage','memory','battery_health','accompaniments','carrier_lock','is_active']);
createAdminCrudRoutes('jobs', 'jobs', ['title','company','company_logo','salary_min','salary_max','currency','location','job_type','description','requirements','benefits','is_featured','is_active']);
createAdminCrudRoutes('profiles', 'user_profiles', ['full_name','title','photo','email','phone','whatsapp','linkedin','location','experience_years','education','current_company','work_experience','technical_skills','certifications','education_details','projects','languages','is_active']);
createAdminCrudRoutes('pages', 'pages', ['slug','title','content','meta_description','is_active']);
createAdminCrudRoutes('settings', 'site_settings', ['setting_key','setting_value']);

// Public API routes
app.get('/api/health', (req, res) => {
  res.json({ status: 'OK', message: 'Backend is running' });
});

// Sliders
app.get('/api/sliders', async (req, res) => {
  try {
    const [rows] = await db.execute('SELECT * FROM sliders WHERE is_active = 1 ORDER BY sort_order');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Main categories
app.get('/api/main-categories', async (req, res) => {
  try {
    const [rows] = await db.execute('SELECT * FROM main_categories WHERE is_active = 1 ORDER BY sort_order');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Popular categories
app.get('/api/popular-categories', async (req, res) => {
  try {
    const [rows] = await db.execute('SELECT * FROM popular_categories WHERE is_active = 1 ORDER BY sort_order');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Business categories
app.get('/api/business-categories', async (req, res) => {
  try {
    const [rows] = await db.execute('SELECT * FROM business_categories WHERE is_active = 1 ORDER BY sort_order');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Classified categories
app.get('/api/classified-categories', async (req, res) => {
  try {
    const [rows] = await db.execute('SELECT * FROM classified_categories WHERE is_active = 1 ORDER BY sort_order');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Pages
app.get('/api/pages', async (req, res) => {
  try {
    const [rows] = await db.execute('SELECT * FROM pages WHERE is_active = 1 ORDER BY title');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Settings
app.get('/api/settings', async (req, res) => {
  try {
    const [rows] = await db.execute('SELECT setting_key, setting_value FROM site_settings ORDER BY setting_key');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Classified sections
app.get('/api/classified-sections', async (req, res) => {
  try {
    const [rows] = await db.execute('SELECT * FROM classified_sections WHERE is_active = 1 ORDER BY sort_order');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Businesses
app.get('/api/businesses', async (req, res) => {
  try {
    let query = 'SELECT b.*, bc.name as category_name FROM businesses b LEFT JOIN business_categories bc ON b.category_id = bc.id WHERE b.is_active = 1';
    const params = [];

    if (req.query.category) {
      query += ' AND b.category_id = ?';
      params.push(req.query.category);
    }

    query += ' ORDER BY b.created_at DESC';

    if (req.query.limit) {
      query += ' LIMIT ?';
      params.push(parseInt(req.query.limit));
    }

    const [rows] = await db.execute(query, params);
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Classifieds - single item
app.get('/api/classifieds/:id', async (req, res) => {
  try {
    const [rows] = await db.execute(
      'SELECT c.*, cc.name as category_name, cs.name as section_name FROM classifieds c LEFT JOIN classified_categories cc ON c.category_id = cc.id LEFT JOIN classified_sections cs ON c.section_id = cs.id WHERE c.id = ? AND c.is_active = 1',
      [req.params.id]
    );
    if (rows.length === 0) {
      return res.status(404).json({ error: 'Classified not found' });
    }
    res.json(rows[0]);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Classifieds
app.get('/api/classifieds', async (req, res) => {
  try {
    let query = 'SELECT c.*, cc.name as category_name, cs.name as section_name FROM classifieds c LEFT JOIN classified_categories cc ON c.category_id = cc.id LEFT JOIN classified_sections cs ON c.section_id = cs.id WHERE c.is_active = 1';
    const params = [];

    if (req.query.section) {
      query += ' AND c.section_id = ?';
      params.push(req.query.section);
    }

    query += ' ORDER BY c.created_at DESC';

    if (req.query.limit) {
      query += ' LIMIT ?';
      params.push(parseInt(req.query.limit));
    }

    const [rows] = await db.execute(query, params);
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Jobs
app.get('/api/jobs', async (req, res) => {
  try {
    let query = 'SELECT * FROM jobs WHERE is_active = 1';
    const params = [];

    query += ' ORDER BY posted_at DESC';

    if (req.query.limit) {
      query += ' LIMIT ?';
      params.push(parseInt(req.query.limit));
    }

    const [rows] = await db.execute(query, params);
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// User profiles
app.get('/api/profiles', async (req, res) => {
  try {
    const [rows] = await db.execute('SELECT * FROM user_profiles WHERE is_active = 1 ORDER BY created_at DESC');
    res.json(rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Stats
app.get('/api/stats', async (req, res) => {
  try {
    const [businesses] = await db.execute('SELECT COUNT(*) as count FROM businesses WHERE is_active = 1');
    const [jobs] = await db.execute('SELECT COUNT(*) as count FROM jobs WHERE is_active = 1');
    const [classifieds] = await db.execute('SELECT COUNT(*) as count FROM classifieds WHERE is_active = 1');

    res.json({
      businesses: businesses[0].count,
      jobs: jobs[0].count,
      classifieds: classifieds[0].count
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Start server
async function startServer() {
  await connectDB();
  app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
  });
}

startServer().catch(console.error);

module.exports = app;