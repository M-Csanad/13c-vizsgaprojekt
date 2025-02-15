const APIFetch = async (url, method, body = null, encode = true) => {
  try {
    const params = {
      method: method,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
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
      if (encode) {
        params.body = JSON.stringify(body);
        params.headers["Content-Type"] = "application/json";
      } else {
        params.body = new URLSearchParams(body).toString();
        params.headers["Content-Type"] = "application/x-www-form-urlencoded";
      }
    }

    const response = await fetch(url, params);

    return response;
  } catch (e) {
    return e;
  }
};

export default APIFetch;
