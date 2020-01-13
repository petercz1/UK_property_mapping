class GetBackend {
  constructor(criteria) {
    return this.get_addresses(criteria)
  }

  async get_addresses(search_criteria) {
    console.log('getting addresses...');
    // build GET url
    let url = new URL(`${window.location.origin}/../backend/main.php`);

    // set request as POST
    let init = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(search_criteria)
    };

    // FETCH it and deal with any network errors
    let response = await fetch(url, init);

    // deal with the response
    if (!response.ok) {
      throw new Error('HTTP error, status = ' + response.status);
    }
    this.addresses = await response.json();
    return this.addresses
  }
  
}

export default GetBackend;