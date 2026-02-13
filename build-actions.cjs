const fs = require('node:fs/promises');
const ncc = require('@vercel/ncc');

(async () => {
    const actionsPath = `${__dirname}/actions`;

    if (!await isDirectory(actionsPath)) {
        return;
    }

    const actionsDirectory = (await fs
        .readdir(actionsPath, { withFileTypes: true }))
        .filter((dirent) => dirent.isDirectory());

    for (const dirent of actionsDirectory)
    {
        const actionPath = `${actionsPath}/${dirent.name}`;
        const fileChecks = await Promise.all([
            isFile(`${actionPath}/action.yml`),
            isFile(`${actionPath}/main.ts`)
        ]);

        if (!fileChecks.every((exists) => exists))
        {
            continue;
        }

        const { code } = await ncc(`${actionPath}/main.ts`, {
            minify: true,
            quiet: true
        });

        await fs.writeFile(`${actionPath}/main.js`, code)
    }
})();

async function isDirectory(path) {
    try
    {
        return (await fs.lstat(path)).isDirectory();
    }
    catch
    {
        return false;
    }
}

async function isFile(path) {
    try
    {
        return (await fs.lstat(path)).isFile();
    }
    catch
    {
        return false;
    }
}
