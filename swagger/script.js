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
  postmanCollection.variable.forEach(variable => {
    if (variable.key === "host" || variable.key === "path_host") {
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
  postmanCollection.item.forEach((item) => {
    let tag = item.name;
    item.item.forEach((subItem) => {
      // console.log(subItem.request.url.path);
      let url = '/path/no/encontrado';
      if(subItem.request.url != undefined){
        url = subItem.request.url.path.join('/');
      }
      const method = subItem.request.method.toLowerCase();
      const body = subItem.request.body ? JSON.parse(replaceVariables(subItem.request.body.raw)) : null;
      const summary = replaceVariables(subItem.name);
      if (!yamlString.includes(`${url}:`)) { // Check if URL already exists
        yamlString += `  /${url}:\n`;
      }

      yamlString += `    ${method}:\n`;
      yamlString += `      tags:\n`;
      yamlString += `       - ${tag}\n`;
      yamlString += `      summary: ${summary}\n`;
      if (body) {
        yamlString += `      requestBody:\n`;
        yamlString += `        required: true\n`;
        yamlString += `        content:\n`;
        yamlString += `          application/json:\n`;
        yamlString += `            schema:\n`;
        yamlString += `              type: object\n`;
        yamlString += `              properties:\n`;
        for (const key in body) {
          yamlString += `                ${key}:\n`;
          // Assuming simple type properties for now
          yamlString += `                  type: string\n`;
        }
      }
      yamlString += `      responses:\n`;
      yamlString += `        '200':\n`;
      yamlString += `          description: OK\n`;
      // Assuming no response body for now
    });
  });

  return yamlString;
}