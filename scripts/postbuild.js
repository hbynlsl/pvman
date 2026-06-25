import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

// 源目录和目标目录
const publicDir = path.join(__dirname, '..', 'public')
const viewDir = path.join(__dirname, '..', 'app', 'view')

// 确保目标目录存在
if (!fs.existsSync(viewDir)) {
  fs.mkdirSync(viewDir, { recursive: true })
}

// 需要移动的 HTML 文件
const htmlFiles = ['index.html']

htmlFiles.forEach((filename) => {
  const srcPath = path.join(publicDir, filename)
  const destPath = path.join(viewDir, filename)
  
  if (fs.existsSync(srcPath)) {
    fs.copyFileSync(srcPath, destPath)
    fs.unlinkSync(srcPath)
    console.log(`✓ 已移动 ${filename} 到 app/view/`)
  } else {
    console.log(`⚠ 文件 ${filename} 不存在`)
  }
})

console.log('\n✓ 构建后处理完成！')
