//@ts-nocheck
const { exec } = require("child_process");
const path = require("path");
const fs = require("fs");

/**
 * Acrescente uma lista de arquivos que se deseja criar um bundle
 */
const bundleFiles = [
  'views/assets/app.ts',
  /*
    'assets/path/tots/',
    {file:'assets/ts/index.ts', args:'--bundle --outfile="assets/ts/index2.js" --platform=browser --format=iife'},
    */
];

const total = bundleFiles.length;
var current = 0;

bundleFiles.forEach((file) => {

    function verificarArquivo(file) {
        try {
            fs.statSync(file);
            return true;
        } catch (e) {
            console.log(`❌ Arquivo ${file} não encontrado!`);
            return false;
        }
    }

    if(typeof file == 'object') {
        if(file.file == undefined || typeof file.file != 'string') {
            console.log(`❌ Objeto dentro de bundleFiles precisa ter o atributo string "file"!`);
            return;
        }
        if(file.args == undefined || typeof file.args != 'string') {
            console.log(`⚠️ Objeto ${file.file} não possui o atributo string "args". Assumindo: "--bundle --minify --outfile="[OUTFILE]" --platform=browser --format=iife"`);
            return;
        }

        if(!verificarArquivo(file.file)) {
            return;
        }

        bundleFile(path.resolve(__dirname, file.file), file.args);
        return;
    }



    if(!verificarArquivo(file)) {
        return;
    }
    const stat = fs.statSync(file);

    if (stat.isDirectory()) {
        walk(path.resolve(__dirname, file), bundleFile);
    }

    if (stat.isFile() && file.endsWith(".ts")) {
        bundleFile(path.resolve(__dirname, file));
    }

    if(!stat.isDirectory() && !stat.isFile()) {
        console.log(`❌ Arquivo ${file} nao encontrado!`);
    }
});


//bundleFile(path.resolve(__dirname, "assets/ts/index.ts"));
//walk(path.resolve(__dirname, "assets"), bundleFile);

function bundleFile(tsFilePath, command = '') {
  const jsOutfile = tsFilePath.replace(/\.ts$/, ".js");
    let cmd;
  if(command == '') {
    console.log(`📦 Bundling: ${tsFilePath}`);
    cmd = `npx esbuild "${tsFilePath}" --bundle --minify --outfile="${jsOutfile}" --platform=browser --format=iife`;
  } else {
    console.log(`📦 Bundling: ${tsFilePath} ${command}`);
    cmd = `npx esbuild "${tsFilePath}" ${command}`;
  }

/*
  let cmd = '';
    command = command.replace('[OUTFILE]', jsOutfile);
    let cmd = `npx esbuild "${tsFilePath}" ${command}`;
  } else {
}
*/
//let cmd = `npx esbuild "${tsFilePath}" --bundle --minify --outfile="${jsOutfile}" --platform=browser --format=iife`;


  exec(cmd, (err, stdout, stderr) => {
    if (err) {
      console.error(`❌ Erro ao processar ${tsFilePath}:\n`, stderr);
    } else {
      console.log(`✅ Gerado: ${jsOutfile}`);
    }
    current++;
  });
}

function walk(dir, callback) {
  fs.readdirSync(dir).forEach((f) => {
    const fullPath = path.join(dir, f);
    const stat = fs.statSync(fullPath);
    if (stat.isDirectory()) {
      walk(fullPath, callback);
    } else if (stat.isFile() && fullPath.endsWith(".ts")) {
      callback(fullPath);
    }
  });
}

setInterval(() => {
    if(current == total) {
        console.log("🎉 Processamento concluído!");
        process.exit(0);
    }
}, 1000);

