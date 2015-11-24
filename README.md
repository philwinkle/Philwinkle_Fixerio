Philwinkle_Fixerio
==

This module adds the Fixer.io webservice to the list of available currency conversion webservices in Magento. 


Enable
--

To enable navigate to `System > Config > Currency Setup` and select Fixer.io from the dropdown. Ensure that your shop is set to allow multiple currencies by, of course, selecting multiple currencies from the **allowed currencies** setting.

Populate rates
--

Now navigate to `System > Manage Currency > Rates`.

Select Fixer.io from the dropdown and select "Import" to begin an import. You will need to save the data values after run by clicking "Save" in the upper-right hand corner.

<img src="http://i.imgur.com/SpG5eOF.png"/>


Automated Nightly Job
--

Import the rates nightly by setting up the job to do so. Navigate to `System > Config > Currency Settings > Scheduled Import Settings` and select Fixer.io from the dropdown. Also enable the service if not previously enabled.

<img src="http://i.imgur.com/1gzprOy.png"/>

License
==

Copyright (c) 2015 Philwinkle LLC / Phillip Jackson <philwinkle@gmail.com>



Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:



The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.



THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.


