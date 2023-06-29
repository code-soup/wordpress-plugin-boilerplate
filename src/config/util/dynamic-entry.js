const fs = require('fs');
const path = require('path');
const dir = process.cwd() + '/src/entry/';

function readJsonFiles(dir, result = {}) {
    const files = fs.readdirSync(dir);

    files.forEach((file) => {
        const filePath = path.join(dir, file);
        const stats = fs.statSync(filePath);

        if (stats.isDirectory()) {
            result[file] = readJsonFiles(filePath, {});
        } else if (stats.isFile() && path.extname(file) === '.js') {
            const fileContent = fs.readFileSync(filePath);
            const data = JSON.parse(fileContent);
            const folderName = path.basename(dir);
            const fileName = path.parse(file).name;
            const key =
                'common' !== fileName
                    ? `${folderName}-${fileName}`
                    : folderName;

            if (!result[key]) {
                result[key] = {};
            }
            result[key] = data.entry;
        }
    });

    return result;
}

const entries = readJsonFiles(dir);
const formatted = Object.assign({}, ...Object.values(entries));

module.exports = formatted;
