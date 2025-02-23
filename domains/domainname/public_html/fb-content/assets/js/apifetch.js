const APIFetch = async (url, method, body = null, encode = true) => {
  try {
    const params = {
      method: method,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      }
    };

    if (["GET", "DELETE"].includes(method) && body) {
      const paramString = Object.keys(body)
        .map(
          (key) => `${encodeURIComponent(key)}=${encodeURIComponent(body[key])}`
        )
        .join("&");
      url += "?" + paramString;
    }

    if (body && method !== "GET") {
      if (body instanceof FormData) {
        if (encode) {
          const jsonObject = {};
          body.forEach((value, key) => {
            jsonObject[key] = value;
          });
          params.body = JSON.stringify(jsonObject);
        } else {
          params.body = body;
        }
      } else {
        params.body = encode ? JSON.stringify(body) : body;
      }
    }

    const response = await fetch(url, params);

    return response;
  } catch (e) {
    return e;
  }
};

export default APIFetch;
