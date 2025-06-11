function convertPostmanToYaml(postmanCollection) {

  let yamlString = "";

  // Function to replace variables with their values
  function replaceVariables(text) {
    if (!text || typeof text !== "string") return text;
    return text.replace(/{{([^}]+)}}/g, (match, variableName) => {
      const variable = postmanCollection.variable.find(v => v.key === variableName);
      return variable ? variable.value : match; // Return original match if variable not found
    });
  }


  yamlString += `openapi: 3.0.0\n`;
  // Information section (replaced variables in name and description)
  yamlString += `info:\n`;
  yamlString += `  title: "${replaceVariables(postmanCollection.info.name)}"\n`;
  yamlString += `  version: "1.0.0"\n`;
  // yamlString += `  description: "${replaceVariables(postmanCollection.info.description)}"\n`;

  // Sección servers
  yamlString += "servers:\n";
  let server = "";

  if (typeof postmanCollection.variable == 'undefined') {
    postmanCollection.variable = [];
  }
  postmanCollection.variable.forEach(variable => {
    if (variable.key === "host") {
      server += variable.value;
    }
  });
  yamlString += `  - url: ${server}\n`;

  // Sección tags
  yamlString += "tags:\n";
  postmanCollection.item.forEach(item => {
    yamlString += `  - name: ${item.name}\n`;
  });

  // Paths section (replace variables in url)
  yamlString += `paths:\n`;

  let paths = [];
  postmanCollection.item.forEach((item) => {
    let tag = item.name;

    item.item.forEach((subItem,index) => {
      let path = '';
      let url = `/path/no/encontrado_${index}`;
      if (subItem.request.url != undefined) {
        url = replaceVariables(subItem.request.url.path.join('/'));
      }

      const method = subItem.request.method.toLowerCase();
      let body = null;
      if (subItem.request.body) {
        body = subItem.request.body.mode == 'raw' && subItem.request.body.raw.length > 0 ? JSON.parse(replaceVariables(subItem.request.body.raw)) : null;

      }
      const summary = replaceVariables(subItem.name);

      if (paths[url] == undefined) {
        // path += `  /${url}:\n`;
      }

      path = `    ${method}:\n`;
      path += `      tags:\n`;
      path += `       - ${tag}\n`;
      path += `      summary: ${summary}\n`;
      if (body) {
        path += `      requestBody:\n`;
        path += `        required: true\n`;
        path += `        content:\n`;
        path += `          application/json:\n`;
        path += `            schema:\n`;
        path += `              type: object\n`;
        path += `              properties:\n`;
        for (const key in body) {

          let typeOf = body[key] == null ? "string" : typeof body[key];
          path += `                ${key}:\n`;
          // Assuming simple type properties for now
          path += `                  type: ${typeOf}\n`;
        }
      }
      path += `      responses:\n`;
      path += `        '200':\n`;
      path += `          description: OK\n`;

      paths[url] = [...(paths[url] ?? []), path];

      // Assuming no response body for now
    });



  });

  for (const url in paths) {
    // path += `  /${url}:\n`;
    let data = '';
    data = `  /${url}:\n`;
    data += paths[url].join("\n");
    yamlString += data;
  }

  return yamlString;
}